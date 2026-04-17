# Product CRUD Source Snapshot

This file bundles the current Product CRUD backend and admin frontend source in one markdown document.

## Backend

### `app/Models/Product.php`

```php
<?php

namespace App\Models;

use App\Collections\ProductCollection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'price', 'description', 'category_id', 'stock', 'image'];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function newCollection(array $models = []): ProductCollection
    {
        return new ProductCollection($models);
    }
}
```

### `app/Http/Controllers/Admin/ProductController.php`

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Services\Customer\ProductService;
use Exception;
use Illuminate\Cache\TaggableStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    public function __construct(protected ProductService $productService)
    {
    }

    public function index(Request $request): View
    {
        $startedAt = microtime(true);
        $result = $this->productService->getAllProducts($request);
        $categories = $this->productService->getAllCategories();
        $loadTimeMs = round((microtime(true) - $startedAt) * 1000, 2);

        Log::channel('products')->info('Admin product index loaded', [
            'cache_key' => $result['cache_key'],
            'duration_ms' => $loadTimeMs,
        ]);

        return view('admin.products.index', [
            'products' => $result['products'],
            'total_products' => $result['total'],
            'categories' => $categories,
            'selected_category' => $request->query('category_id'),
            'page_title' => 'Manage Products',
            'listing_route' => route('admin.products.index'),
            'load_time_ms' => $loadTimeMs,
        ]);
    }

    public function create(): View
    {
        $categories = $this->productService->getAllCategories();

        Log::channel('products')->info('Create product page opened', [
            'user_id' => auth()->id(),
        ]);

        return view('admin.products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        Log::channel('products')->info('Controller: Store product');

        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $this->productService->createProduct($data);
        $this->flushProductAndAdminCaches();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully');
    }

    public function edit(Product $product): View
    {
        Log::info('Controller: Edit page');

        $categories = $this->productService->getAllCategories();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        try {
            Log::info('Controller: Update product');

            $data = $request->validated();

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            $this->productService->updateProduct($data, $product);
            Cache::forget('product_'.$product->id);
            Cache::forget('product.'.$product->id);
            $this->flushProductAndAdminCaches();

            return redirect()->route('admin.products.index')
                ->with('success', 'Product updated successfully');
        } catch (Exception $e) {
            Log::channel('products')->error('Controller: Update failed', [
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Update failed');
        }
    }

    public function destroy(Product $product): RedirectResponse
    {
        try {
            Log::channel('products')->warning('Controller: Delete product');

            $this->productService->deleteProduct($product);
            Cache::forget('product_'.$product->id);
            Cache::forget('product.'.$product->id);
            $this->flushProductAndAdminCaches();

            return redirect()->route('admin.products.index')
                ->with('success', 'Product deleted successfully');
        } catch (Exception $e) {
            Log::channel('products')->error('Controller: Delete failed', [
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Delete failed');
        }
    }

    public function export(Request $request): StreamedResponse
    {
        return response()->streamDownload(function () {
            $file = fopen('php://output', 'w');
            $filters = $request->only(['search', 'category_id', 'sort']);

            $products = $this->productService->getProductsForExport($filters);

            fputcsv($file, ['name', 'price', 'description']);

            foreach ($products as $product) {
                fputcsv($file, [$product->name, $product->price, $product->description]);
            }
        }, 'products.csv');
    }

    private function flushProductAndAdminCaches(): void
    {
        $this->productService->flushProductCaches();

        if (Cache::getStore() instanceof TaggableStore) {
            Cache::tags(['admin'])->flush();
            return;
        }

        Cache::forget('admin.dashboard.stats');
        Cache::forget('admin.recent.orders');
    }
}
```

### `routes/web.php` admin wrapper

```php
Route::middleware(['auth', 'checkrole:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        require __DIR__.'/admin.php';
    });
```

### `routes/admin.php`

```php
<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SalesAnalyticsController;
use App\Http\Controllers\Admin\CacheMonitorController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [AdminDashboardController::class, 'index'])
    ->name('dashboard');

Route::resource('categories', CategoryController::class)->except(['show']);

Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/export', [ProductController::class, 'export'])->name('export');
    Route::get('/create', [ProductController::class, 'create'])->name('create');
    Route::post('/', [ProductController::class, 'store'])->name('store');
    Route::get('/{product}/edit', [ProductController::class, 'edit'])->whereNumber('product')->name('edit');
    Route::put('/{product}', [ProductController::class, 'update'])->whereNumber('product')->name('update');
    Route::delete('/{product}', [ProductController::class, 'destroy'])->whereNumber('product')->name('destroy');
});

Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

Route::prefix('orders')->name('orders.')->group(function () {
    Route::get('/', [AdminOrderController::class, 'index'])->name('index');
    Route::get('/{order}', [AdminOrderController::class, 'show'])->name('show');
    Route::put('/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('status');
});

Route::prefix('customers')->name('customers.')->group(function () {
    Route::get('/', [CustomerController::class, 'index'])->name('index');
    Route::get('/{customer}', [CustomerController::class, 'show'])->whereNumber('customer')->name('show');
});

Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');

Route::get('/reports', [SalesAnalyticsController::class, 'index'])->name('reports.index');
Route::get('/reports/export', [SalesAnalyticsController::class, 'export'])->name('reports.export');
Route::get('/reports/download/{file}', [ReportController::class, 'download'])
    ->name('reports.download');

Route::prefix('cache')->name('admin.cache.')->group(function () {

    Route::get('/', [CacheMonitorController::class, 'index'])
        ->name('index');

    Route::post('/clear', [CacheMonitorController::class, 'clear'])
        ->name('clear');

    Route::post('/clear-tag/{tag}', [CacheMonitorController::class, 'clearTag'])
        ->name('clearTag');

});

Route::view('/logs', 'admin.logs.index')->name('logs.index');
```

## Migrations

### `database/migrations/2026_03_10_101233_create_products_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price',10,2);
            $table->text('description')->nullable();
            $table->string('category');
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```

### `database/migrations/2026_03_20_070420_add_category_id_to_products_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
```

### `database/migrations/2026_03_20_070512_add_category_id_to_products_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
```

### `database/migrations/2026_03_23_055955_drop_category_column_from_products.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
              $table->dropColumn('category');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
```

### `database/migrations/2026_03_24_051244_add_stock_to_products_table.php`

```php
    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up(): void
        {
            Schema::table('products', function (Blueprint $table) {
                $table->integer('stock')->default(0);
            });
        }

        public function down(): void
        {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('stock');
            });
        }
    };
```

### `database/migrations/2026_04_08_053016_add_is_featured_to_products_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
```

## Frontend

### `resources/views/admin/layouts/app.blade.php`

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - @yield('page-title', 'Admin')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-screen overflow-hidden bg-slate-50 text-slate-900 antialiased">

<div class="h-full flex">

    <!-- ðŸ”¹ SIDEBAR -->
    <aside class="w-64 border-r border-slate-200 bg-white flex flex-col">

        <!-- Top -->
        <div class="border-b px-5 py-5 shrink-0">
            <a href="{{ route('admin.dashboard') }}" class="text-lg font-semibold text-slate-900">
                {{ config('app.name') }}
            </a>
            <p class="mt-1 text-xs uppercase tracking-[0.2em] text-slate-500">
                Admin panel
            </p>
        </div>

        <!-- ðŸ”¥ SCROLLABLE MENU -->
        <div class="flex-1 overflow-y-auto">
            <x-admin.sidebar />
        </div>

    </aside>


    <!-- ðŸ”¹ CONTENT AREA -->
    <div class="flex-1 flex flex-col min-w-0">

        <!-- ðŸ”¹ HEADER -->
<header class="sticky top-0 z-30 border-b bg-white px-6 py-4">
    <div class="flex items-center justify-between">

        <!-- Left -->
        <h1 class="text-xl font-semibold">
            @yield('page-title', 'Dashboard')
        </h1>

        <!-- Right -->
        <div class="flex items-center gap-4">

            <!-- ðŸ”” Notification -->
            <div class="relative">

                <button id="notificationBtn" class="relative text-xl">
                    ðŸ””
                    <span id="notificationCount"
                        class="hidden absolute -top-2 -right-2 rounded-full bg-rose-600 px-1.5 text-xs text-white">
                        0
                    </span>
                </button>

                <!-- Dropdown -->
                <div id="notificationDropdown"
                    class="hidden absolute right-0 mt-2 w-80 rounded-xl border bg-white shadow-lg z-50">

                    <div class="flex items-center justify-between border-b p-3">
                        <span class="font-semibold">Notifications</span>
                        <button id="markAllNotificationsBtn"
                            class="text-xs text-slate-500 hover:text-slate-900">
                            Mark all read
                        </button>
                    </div>

                    <div id="notificationList"
                        class="max-h-64 overflow-y-auto text-sm">
                        <div class="p-3 text-slate-500">
                            No notifications
                        </div>
                    </div>

                </div>
            </div>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="bg-slate-900 text-white px-3 py-2 rounded-lg text-sm">
                    Logout
                </button>
            </form>

        </div>

    </div>
</header>

        <!-- ðŸ”¥ SCROLLABLE CONTENT -->
        <main id="main-content" class="flex-1 overflow-y-auto p-6 space-y-4">

            <!-- Toasts -->
            <x-admin.toast tone="success" :message="session('success')" />
            <x-admin.toast tone="danger" :message="session('error')" />
            <x-admin.toast tone="info" :message="session('status')" />

            @yield('content')

        </main>

    </div>

</div>

<!-- Toast container -->
<div id="toast-container" class="fixed top-5 right-5 space-y-2 z-50"></div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
$(document).on('click', '.sidebar-link', function (e) {

    e.preventDefault();

    let url = $(this).attr('href');

    // ðŸ”¥ REMOVE OLD ACTIVE
    $('.sidebar-link')
        .removeClass('bg-slate-900 text-white')
        .addClass('text-slate-600');

    // ðŸ”¥ ADD ACTIVE TO CLICKED
    $(this)
        .addClass('bg-slate-900 text-white')
        .removeClass('text-slate-600');

    $('#main-content').html('<div class="p-6">Loading...</div>');

    $.get(url, function (response) {

        let newContent = $(response).find('#main-content').html();

        $('#main-content').html(newContent);

        window.history.pushState({}, '', url);

    });
});
</script>

@stack('scripts')
</body>
</html>
```

### `resources/views/admin/components/sidebar.blade.php`

```blade
@php
    $items = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'pattern' => 'admin.dashboard'],
        ['label' => 'Products', 'route' => 'admin.products.index', 'pattern' => 'admin.products.*'],
        ['label' => 'Categories', 'route' => 'admin.categories.index', 'pattern' => 'admin.categories.*'],
        ['label' => 'Orders', 'route' => 'admin.orders.index', 'pattern' => 'admin.orders.*'],
        ['label' => 'Customers', 'route' => 'admin.customers.index', 'pattern' => 'admin.customers.*'],
        ['label' => 'Inventory', 'route' => 'admin.inventory.index', 'pattern' => 'admin.inventory.*'],
        ['label' => 'Reports', 'route' => 'admin.reports.index', 'pattern' => 'admin.reports.*'],
    ];
@endphp

<nav class="flex-1 space-y-1 overflow-y-auto p-4 text-sm font-medium">
    @foreach ($items as $item)
        <a
            href="{{ route($item['route']) }}"
            class="sidebar-link block rounded-lg px-3 py-2.5 transition
class="sidebar-link block rounded-lg px-3 py-2.5 text-slate-600 hover:bg-slate-100 hover:text-slate-900"
        >
            {{ $item['label'] }}
        </a>
    @endforeach
</nav>
```

### `resources/views/admin/products/index.blade.php`

```blade
@extends('admin.layouts.app')

@section('page-title', 'Products')

@section('content')
<div class="space-y-5">

    <!-- ðŸ”¹ Stats -->
    <div class="grid grid-cols-3 gap-4">
        <div class="rounded-xl border bg-white p-4">
            <p class="text-xs text-slate-500">Total Products</p>
            <p class="text-xl font-semibold mt-1">{{ $products->total() }}</p>
        </div>

        <div class="rounded-xl border bg-white p-4">
            <p class="text-xs text-slate-500">Low Stock</p>
            <p class="text-xl font-semibold text-amber-600 mt-1">
                {{ $products->where('stock','<',10)->count() }}
            </p>
        </div>

        <div class="rounded-xl border bg-white p-4">
            <p class="text-xs text-slate-500">Out of Stock</p>
            <p class="text-xl font-semibold text-rose-600 mt-1">
                {{ $products->where('stock',0)->count() }}
            </p>
        </div>
    </div>

    <!-- ðŸ”¹ Filters -->
    <div class="rounded-xl border bg-white p-4">
        <form id="filter-form" class="grid gap-3 md:grid-cols-4">

            <input type="text" name="search"
                value="{{ request('search') }}"
                placeholder="Search product..."
                class="w-full rounded-lg border px-3 py-2 text-sm">

            <select name="category_id" class="rounded-lg border px-3 py-2 text-sm">
                <option value="">All categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}"
                        @selected(request('category_id') == $cat->id)>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>

            <select name="sort" class="rounded-lg border px-3 py-2 text-sm">
                <option value="newest">Newest</option>
                <option value="price_asc">Price Low-High</option>
                <option value="price_desc">Price High-Low</option>
            </select>


        </form>
    </div>

    <!-- ðŸ”¹ Table -->
    <div id="product-table" class="rounded-xl border bg-white">

        <!-- Header -->
        <div class="flex justify-between items-center px-4 py-3 border-b">
            <p class="text-sm text-slate-500">
                Showing {{ $products->firstItem() }}â€“{{ $products->lastItem() }}
            </p>

            <div class="flex gap-2">
            <button 
                onclick="exportProducts()"
                class="border px-3 py-1.5 rounded-lg text-sm">
                Export
            </button>

                <a href="{{ route('admin.products.create') }}"
                   class="bg-slate-900 text-white px-3 py-1.5 rounded-lg text-sm">
                    + Add
                </a>
            </div>
        </div>

        <!-- Table -->
        <table class="w-full text-sm">

            <thead class="text-xs text-slate-500 border-b">
                <tr>
                    <th class="px-4 py-2 text-left">Product</th>
                    <th class="px-4 py-2 text-left">Price</th>
                    <th class="px-4 py-2 text-left">Stock</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($products as $product)
                    @php $stock = (int)$product->stock; @endphp

                    <tr id="row-{{ $product->id }}" class="border-b hover:bg-slate-50">

                        <!-- Product -->
                        <td class="px-4 py-3">
                            <p class="font-medium text-slate-900">
                                {{ $product->name }}
                            </p>
                            <p class="text-xs text-slate-500">
                                {{ $product->category->name ?? 'Uncategorized' }}
                            </p>
                        </td>

                        <!-- Price -->
                        <td class="px-4 py-3">
                            â‚¹ {{ number_format($product->price, 2) }}
                        </td>

                        <!-- Stock -->
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded border
                                {{ $stock == 0 ? 'text-red-600' :
                                   ($stock < 10 ? 'text-amber-600' : 'text-green-600') }}">
                                {{ $stock == 0 ? 'Out' : $stock }}
                            </span>
                        </td>

                        <!-- Actions -->
                        <td class="px-4 py-3">
                            <div class="flex gap-2">

                                <a href="{{ route('admin.products.edit', $product) }}"
                                   class="border px-3 py-1 rounded text-xs">
                                    Edit
                                </a>

                                <button 
                                    onclick="deleteProduct({{ $product->id }})"
                                    class="bg-red-600 text-white px-3 py-1 rounded text-xs">
                                    Delete
                                </button>

                            </div>
                        </td>

                    </tr>

                @empty
                    <tr>
                        <td colspan="4" class="text-center py-8 text-slate-500">
                            No products found.
                        </td>
                    </tr>
                @endforelse

            </tbody>

        </table>

        <!-- Pagination -->
        <div class="p-4">
            {{ $products->links() }}
        </div>

    </div>

</div>
@endsection


@push('scripts')
<script>

function showToast(message, tone = 'success') {

    let classes = {
        success: 'border-emerald-200 bg-emerald-50 text-emerald-800',
        danger: 'border-rose-200 bg-rose-50 text-rose-800',
        warning: 'border-amber-200 bg-amber-50 text-amber-800',
        default: 'border-slate-200 bg-white text-slate-700'
    };

    let toast = `
        <div class="rounded-xl border px-4 py-3 text-sm shadow-sm ${classes[tone]}">
            ${message}
        </div>
    `;

    $('#toast-container').append(toast);

    setTimeout(() => {
        $('#toast-container div').first().fadeOut(300, function () {
            $(this).remove();
        });
    }, 2500);
}

function deleteProduct(id) {

    $.ajax({
        url: `/admin/products/${id}`,
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            _method: 'DELETE'
        },

        success: function () {

            $('#row-' + id).fadeOut(200, function () {
                $(this).remove();
            });

            showToast('Product deleted successfully', 'success');
        },

        error: function () {
            showToast('Delete failed', 'danger');
        }
    });
}

function loadProducts() {

    let formData = $('#filter-form').serialize();

    $.get("{{ route('admin.products.index') }}", formData, function (response) {

        let html = $(response).find('#product-table').html();

        $('#product-table').html(html);
    });
}

$('#filter-form input, #filter-form select').on('change keyup', function () {
    loadProducts();
});

function exportProducts() {

    showToast('Preparing export...', 'warning');

    let params = $('#filter-form').serialize();

    $.ajax({
        url: "{{ route('admin.products.export') }}",
        type: "GET",
        data: params,

        success: function (response) {

            if (response.download_url) {
                window.location.href = response.download_url;

                showToast('Export ready. Download started.', 'success');
            }
        },

        error: function () {
            showToast('Export failed', 'danger');
        }
    });
}

</script>
@endpush
```

### `resources/views/admin/products/create.blade.php`

```blade
@extends('admin.layouts.app')

@section('page-title', 'Create Product')

@section('content')
<x-admin.card title="Create product" description="Add a new item to the catalog.">
    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="grid gap-4 md:grid-cols-2">
        @csrf

        <x-admin.input name="name" label="Product name" :value="old('name')" required />
        <x-admin.select name="category_id" label="Category" :options="$categories->pluck('name', 'id')->all()" :selected="old('category_id')" placeholder="Select category" required />
        <x-admin.input name="price" label="Price" type="number" :value="old('price')" step="0.01" required />
        <x-admin.input name="stock" label="Stock" type="number" :value="old('stock')" required />

        <div class="md:col-span-2">
            <label class="mb-1 block text-sm font-medium text-slate-700">Description</label>
            <textarea name="description" rows="5" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200">{{ old('description') }}</textarea>
            @error('description')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @else
                <p class="text-xs text-transparent">.</p>
            @enderror
        </div>

        <div class="md:col-span-2">
            <label class="mb-1 block text-sm font-medium text-slate-700">Image</label>
            <input type="file" name="image" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm file:mr-4 file:rounded-lg file:border-0 file:bg-slate-900 file:px-3 file:py-2 file:text-sm file:font-medium file:text-white">
            @error('image')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @else
                <p class="text-xs text-transparent">.</p>
            @enderror
        </div>

        <div class="md:col-span-2 flex items-center justify-end gap-2 pt-2">
            <x-admin.button href="{{ route('admin.products.index') }}" variant="secondary">Cancel</x-admin.button>
            <x-admin.button type="submit">Save Product</x-admin.button>
        </div>
    </form>
</x-admin.card>
@endsection
```

### `resources/views/admin/products/edit.blade.php`

```blade
@extends('admin.layouts.app')

@section('page-title', 'Edit Product')

@section('content')
<x-admin.card title="Edit product" description="Update catalog details and inventory.">
    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="grid gap-4 md:grid-cols-2">
        @csrf
        @method('PUT')

        <x-admin.input name="name" label="Product name" :value="$product->name" required />
        <x-admin.select name="category_id" label="Category" :options="$categories->pluck('name', 'id')->all()" :selected="$product->category_id" placeholder="Select category" required />
        <x-admin.input name="price" label="Price" type="number" :value="$product->price" step="0.01" required />
        <x-admin.input name="stock" label="Stock" type="number" :value="$product->stock" required />

        <div class="md:col-span-2">
            <label class="mb-1 block text-sm font-medium text-slate-700">Description</label>
            <textarea name="description" rows="5" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200">{{ $product->description }}</textarea>
            @error('description')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @else
                <p class="text-xs text-transparent">.</p>
            @enderror
        </div>

        <div class="md:col-span-2 grid gap-4 md:grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="mb-3 text-sm font-medium text-slate-700">Current image</p>
                @if ($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-44 w-full rounded-lg object-cover">
                @else
                    <div class="flex h-44 items-center justify-center rounded-lg border border-dashed border-slate-300 text-sm text-slate-500">No image</div>
                @endif
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Replace image</label>
                <input type="file" name="image" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm file:mr-4 file:rounded-lg file:border-0 file:bg-slate-900 file:px-3 file:py-2 file:text-sm file:font-medium file:text-white">
                @error('image')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @else
                    <p class="text-xs text-transparent">.</p>
                @enderror
            </div>
        </div>

        <div class="md:col-span-2 flex items-center justify-end gap-2 pt-2">
            <x-admin.button href="{{ route('admin.products.index') }}" variant="secondary">Cancel</x-admin.button>
            <x-admin.button type="submit">Update Product</x-admin.button>
        </div>
    </form>
</x-admin.card>
@endsection
```


### `app/Http/Requests/StoreProductRequest.php`

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    /**
     * Get the validation rules that apply to this request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:products,name',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please enter product name.',
            'name.unique' => 'This product name already exists.',

            'price.required' => 'Please enter price.',
            'price.numeric' => 'Price must be a number.',
            'price.min' => 'Price must be greater than 0.',

            'stock.required' => 'Please enter stock quantity.',
            'stock.integer' => 'Stock must be a number.',
            'stock.min' => 'Stock cannot be negative.',

            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'Selected category is invalid.',

            'image.required' => 'Please upload a product image.',
            'image.image' => 'File must be an image.',
            'image.mimes' => 'Image must be jpg, jpeg or png.',
            'image.max' => 'Image size must be less than 2MB.',
        ];
    }
}
```

### `app/Http/Requests/UpdateProductRequest.php`

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    /**
     * Get the validation rules that apply to this request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ];
    }
}
```

### `app/Services/Customer/ProductService.php`

```php
<?php

namespace App\Services\Customer;

use App\Collections\ProductCollection;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Cache\TaggableStore;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProductService
{
    private const CACHE_KEY_REGISTRY = 'products.cache.keys';

    public function getAllProducts(Request $request, array $overrides = []): array
    {
        $context = $request->routeIs('admin.*') ? 'admin' : 'user';
        $page = max(1, (int) $request->query('page', 1));
        $filters = $this->extractListingFilters($request, $overrides);

        $cacheKey = sprintf(
            'products.list.%s.page.%d.filters.%s',
            $context,
            $page,
            md5(json_encode($filters))
        );

        $products = $this->rememberWithMetrics($cacheKey, now()->addHour(), function () use ($filters, $page, $request) {
            $allProducts = $this->buildProductQuery($filters)->get();

            $filteredProducts = $this->filterProducts($allProducts, $filters);
            $perPage = 9;

            $pageItems = $filteredProducts->forPage($page, $perPage)->values();

            return new LengthAwarePaginator(
                $pageItems,
                $filteredProducts->count(),
                $perPage,
                $page,
                [
                    'path' => $request->url(),
                    'query' => $request->except('page'),
                ]
            );
        });

        return [
            'products' => $products,
            'total' => $products->total(),
            'cache_key' => $cacheKey,
        ];
    }

    public function getProductsByCategory(Request $request, int $categoryId): array
    {
        return $this->getAllProducts($request, [
            'category_ids' => [$categoryId],
        ]);
    }

    public function getProductById(int $productId): Product
    {
        $cacheKey = "product.{$productId}";

        return $this->rememberWithMetrics($cacheKey, now()->addMinutes(30), function () use ($productId) {
            return Product::query()
                ->select([
                    'id',
                    'name',
                    'price',
                    'description',
                    'category_id',
                    'stock',
                    'image',
                    'is_featured',
                    'created_at',
                ])
                ->with('category:id,name')
                ->findOrFail($productId);
        });
    }

    public function getFeaturedProducts(int $limit = 8)
    {
        $cacheKey = 'products.featured';

        return $this->rememberWithMetrics($cacheKey, now()->addHour(), function () use ($limit) {
            return Product::query()
                ->select([
                    'id',
                    'name',
                    'price',
                    'description',
                    'category_id',
                    'stock',
                    'image',
                    'is_featured',
                ])
                ->where('is_featured', true)
                ->with('category:id,name')
                ->latest('id')
                ->take($limit)
                ->get();
        });
    }

    public function getAllCategories()
    {
        return $this->rememberWithMetrics('products.categories', now()->addHours(2), function () {
            return Category::query()->orderBy('name')->get(['id', 'name']);
        });
    }

    public function createProduct(array $data): Product
    {
        Log::channel('products')->info('Creating product');

        return Product::create($data);
    }

    public function updateProduct(array $data, Product $product): Product
    {
        Log::channel('products')->info('Updating product', [
            'product_id' => $product->id,
        ]);

        $product->update($data);

        return $product->fresh();
    }

    public function deleteProduct(Product $product): bool
    {
        Log::channel('products')->warning('Deleting product', [
            'product_id' => $product->id,
        ]);

        return (bool) $product->delete();
    }

    public function getProduct(Product $product): Product
    {
        return $this->getProductById((int) $product->id);
    }

    public function flushProductCaches(): void
    {
        if ($this->supportsTags()) {
            Cache::tags(['products'])->flush();
            Log::channel('products')->info('Product cache flushed using cache tags');

            return;
        }

        $keys = Cache::get(self::CACHE_KEY_REGISTRY, []);

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        Cache::forget(self::CACHE_KEY_REGISTRY);

        Log::channel('products')->info('Product cache flushed using tracked keys', [
            'keys' => count($keys),
        ]);
    }

    public function getProductsForApi()
    {
        return Product::query()
            ->select([
                'id',
                'name',
                'price',
                'description',
                'category_id',
                'stock',
                'image',
                'is_featured',
            ])
            ->with('category:id,name')
            ->latest('id')
            ->get();
    }

    public function getProductsForExport(array $filters = [])
    {
        $query = Product::query();

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['sort'])) {
            match ($filters['sort']) {
                'price_asc' => $query->orderBy('price', 'asc'),
                'price_desc' => $query->orderBy('price', 'desc'),
                default => $query->latest(),
            };
        } else {
            $query->orderBy('name');
        }

        return $query->get(['name', 'price', 'description']);
    }

    private function filterProducts(ProductCollection $products, array $filters): ProductCollection
    {
        if ($filters['search'] !== '') {
            $search = mb_strtolower($filters['search']);

            $products = $products->filter(function ($product) use ($search) {
                $haystack = mb_strtolower(implode(' ', array_filter([
                    (string) $product->name,
                    (string) $product->description,
                    (string) ($product->category?->name ?? ''),
                ])));

                return str_contains($haystack, $search);
            });
        }

        if ($filters['on_sale']) {
            $products = $products->onSale();
        }

        return $products->sortProducts($filters['sort'] ?? 'newest')->values();
    }

    private function extractListingFilters(Request $request, array $overrides = []): array
    {
        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'category_ids' => collect($request->input('category_ids', $request->input('category_id', [])))
                ->filter(fn ($categoryId) => $categoryId !== null && $categoryId !== '')
                ->map(fn ($categoryId) => (int) $categoryId)
                ->values()
                ->all(),
            'min_price' => $request->query('min_price'),
            'max_price' => $request->query('max_price'),
            'sort' => $request->query('sort', 'newest'),
            'in_stock' => $request->boolean('in_stock'),
            'on_sale' => $request->boolean('on_sale'),
        ];

        return array_replace($filters, $overrides);
    }

    private function buildProductQuery(array $filters)
    {
        $query = Product::query()
            ->select([
                'id',
                'name',
                'price',
                'description',
                'category_id',
                'stock',
                'image',
                'is_featured',
                'created_at',
            ])
            ->with('category:id,name')
            ->orderByDesc('created_at');

        if (! empty($filters['category_ids'])) {
            $query->whereIn('category_id', $filters['category_ids']);
        }

        if ($filters['min_price'] !== null && $filters['min_price'] !== '') {
            $query->where('price', '>=', $filters['min_price']);
        }

        if ($filters['max_price'] !== null && $filters['max_price'] !== '') {
            $query->where('price', '<=', $filters['max_price']);
        }

        if ($filters['in_stock']) {
            $query->where('stock', '>', 0);
        }

        if (($filters['sort'] ?? 'newest') === 'popularity') {
            $query->selectSub(function ($subQuery) {
                $subQuery->from('order_items')
                    ->selectRaw('COALESCE(SUM(quantity), 0)')
                    ->whereColumn('order_items.product_id', 'products.id');
            }, 'sales_count');
        }

        return $query;
    }

    private function rememberWithMetrics(string $cacheKey, $ttl, callable $callback)
    {
        $repository = $this->cacheRepository();
        $this->trackCacheKey($cacheKey);

        $cacheHit = $repository->has($cacheKey);
        $start = microtime(true);

        $value = $repository->remember($cacheKey, $ttl, $callback);

        $durationMs = round((microtime(true) - $start) * 1000, 2);

        Log::channel('products')->info('Product cache read', [
            'key' => $cacheKey,
            'hit' => $cacheHit,
            'duration_ms' => $durationMs,
        ]);

        return $value;
    }

    private function cacheRepository()
    {
        return $this->supportsTags()
            ? Cache::tags(['products'])
            : Cache::store();
    }

    private function supportsTags(): bool
    {
        return Cache::getStore() instanceof TaggableStore;
    }

    private function trackCacheKey(string $cacheKey): void
    {
        if ($this->supportsTags()) {
            return;
        }

        $keys = Cache::get(self::CACHE_KEY_REGISTRY, []);

        if (in_array($cacheKey, $keys, true)) {
            return;
        }

        $keys[] = $cacheKey;

        Cache::forever(self::CACHE_KEY_REGISTRY, $keys);
    }
}
```
