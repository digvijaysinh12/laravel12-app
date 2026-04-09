<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(): View
    {
        $products = Product::with('category')
            ->orderBy('stock')
            ->orderBy('name')
            ->paginate(15);

        $lowStockCount = Product::where('stock', '<=', 10)->count();

        return view('admin.inventory.index', compact('products', 'lowStockCount'));
    }
}
