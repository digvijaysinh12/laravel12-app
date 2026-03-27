<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Log;

class ProductService
{
    public function getAllProducts($request)
    {
        Log::channel('products')->info('Fetching products', [
            'search' => $request->search
        ]);

        $query = Product::with('category');

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }

        $total = (clone $query)->count();

        $products = $query->paginate(9)->appends([
            'search' => $request->search
        ]);

        return [
            'products' => $products,
            'total' => $total
        ];
    }

    public function createProduct(array $data)
    {
        Log::channel('products')->info('Creating product');

        return Product::create($data);
    }

    public function updateProduct(array $data, Product $product)
    {
        Log::channel('products')->info('Updating product', [
            'product_id' => $product->id
        ]);

        $product->update($data);

        return $product->fresh();
    }

    public function deleteProduct(Product $product)
    {
        Log::channel('products')->warning('Deleting product', [
            'product_id' => $product->id
        ]);

        return $product->delete();
    }

    public function getProduct(Product $product)
    {
        Log::channel('products')->info('Fetching single product', [
            'product_id' => $product->id
        ]);

        return $product->load('category');
    }
}