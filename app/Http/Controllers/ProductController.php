<?php

namespace App\Http\Controllers;

use App\Exceptions\ProductNotFoundException;
use App\Exceptions\ProductOutOfStockException;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Exception;
use Illuminate\Http\Request;
use App\Facades\Product as ProductFacade;
use Illuminate\Support\Facades\Log;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

        public function index(Request $request)
        {
            $query = Product::with('category');

            if ($request->filled('search')) {
                $query->where('name', 'LIKE', '%' . $request->search . '%');
            }

            $total_products = (clone $query)->count();

            $products = $query->paginate(9)->appends([
                'search' => $request->search
            ]);

            $page_title = "Product List";

            Log::channel('products')->info('Product list viewed',[
                'user_id' => auth()->id(),
                'search' => $request->search
            ]);

            return view('products.index', compact('products', 'total_products', 'page_title'));
        }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();

        Log::channel('products')->info('Create product page opened',[
            'user_id' =>auth()->id()
        ]);

        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function search(Request $request)
    {
        $category = $request->query('category');
        $price = $request->query('price');

        return [
            'category' => $category,
            'price' => $price
        ];
    }

    public function store(StoreProductRequest $request)
    {
        try {

            // 🔹 Log full request (raw input)
            Log::channel('products')->info('Incoming request', [
                'user_id' => auth()->id(),
                'data' => $request->all()
            ]);

            // 🔹 Validation
            $validate = $request->validated();

            Log::channel('products')->info('Validated data', [
                'data' => $validate
            ]);

            // 🔹 Stock check (optional)
            if ($validate['stock'] <= 0) {
                Log::channel('products')->warning('Stock is zero or less', [
                    'data' => $validate
                ]);
            }


            // 🔹 Image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products', 'public');

                Log::channel('products')->info('Image uploaded', [
                    'path' => $imagePath
                ]);

                $validate['image'] = $imagePath;
            }

            // 🔹 Store product
            ProductFacade::store($validate);

            Log::channel('products')->info('Product stored successfully', [
                'user_id' => auth()->id(),
                'product' => $validate
            ]);


            return redirect()->route('products.index')
                ->with('success', 'Product created successfully');

        } catch (ProductOutOfStockException $e) {

            Log::channel('products')->error('Stock exception', [
                'message' => $e->getMessage()
            ]);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            Log::channel('products')->error('General error', [
                'message' => $e->getMessage()
            ]);

            return back()->with('error', 'Product creation failed');
        }
    }

    /**
     * Display the specified resource.
     */
        public function show(Product $product)
        {

            Log::channel('products')->info('Product vieved',[
                'user_id' => auth()->id(),
                'product_id' => $product->id()
            ]);

            return view('products.show', compact('product'));
        }


    /**
     * Show the form for editing the specified resource.
     */

    public function edit(Product $product)
    {
        $categories = Category::all();

        Log::channel('products')->info('Edit product page opened',[
            'user_id' => auth()->id(),
            'product_id' => $product->id
        ]);

        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        try {
            $validated = $request->validated();

            Log::channel('products')->info('Update request received', [
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'data' => $validated
            ]);

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products', 'public');
                $validated['image'] = $imagePath;

                Log::channel('products')->info('Image updated', [
                    'product_id' => $product->id,
                    'path' => $imagePath
                ]);
            }

            ProductFacade::update($validated, $product);

            Log::channel('products')->info('Product updated successfully', [
                'product_id' => $product->id
            ]);

            return redirect()->route('products.index')
                ->with('success', 'Product updated successfully');

        } catch (\Exception $e) {

            Log::channel('products')->error('Update failed', [
                'product_id' => $product->id,
                'message' => $e->getMessage()
            ]);

            return back()->with('error', 'Update failed');
        }
    }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy(Product $product)
    {
        try {
            Log::channel('products')->warning('Product delete requested', [
                'user_id' => auth()->id(),
                'product_id' => $product->id
            ]);

            ProductFacade::delete($product);

            Log::channel('products')->info('Product deleted', [
                'product_id' => $product->id
            ]);

            return redirect()->route('products.index')
                ->with('success', 'Product deleted successfully');

        } catch (\Exception $e) {

            Log::channel('products')->error('Delete failed', [
                'product_id' => $product->id,
                'message' => $e->getMessage()
            ]);

            return back()->with('error', 'Delete failed');
        }
    }

    public function apiProducts()
    {
        Log::channel('products')->info('API products fetched', [
            'user_id' => auth()->id()
        ]);

        $products = Product::all();

        return response()->success($products);
    }
}
