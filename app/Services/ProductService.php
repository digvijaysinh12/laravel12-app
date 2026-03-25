<?php
namespace App\Services;
use App\Exceptions\ProductNotFoundException;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\Log;

class ProductService
{
    public function store(array $data)
    {
        return Product::create($data);
    }

    public function update(array $data, Product $product)
    {
        $product->update($data);
        return $product->fresh();
    }

    public function delete(Product $product)
    {
        return $product->delete();
    }


    public function getAllProducts($request)
    {

        Log::channel('products')->info('ProductService: Fetching products', [
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

        try {
            Log::info('ProductService: creating products', $data);

            if (isset($data['stock'])&&$data['stock'] <= 0) {
                throw new ProductNotFoundException("Stock is zero");
            }

            $product = Product::create($data);


            return $product;

        } catch (Exception $e) {

            Log::channel('products')->error('ProductService: create failed', [
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    public function updateProduct(array $data, Product $product)
    {

        try {
            Log::channel('products')->info('ProductService: Updating product', [
                'product_id' => $product->id,
                'data' => $data
            ]);

            $product->update($data);


            return $product;
        } catch (Exception $e) {

            Log::channel('products')->error('ProductService: update failed', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }


    public function deleteProduct(Product $product)
    {
        try {
            Log::channel('products')->warning('ProductService: Deleting product', [
                'product_id' => $product->id
            ]);

            return $product->delete();
        } catch (Exception $e) {
            Log::channel('products')->error('ProductService: Delete failed', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    public function getProduct(Product $product)
    {
        if (!$product) {
            throw new ProductNotFoundException("Product not found");
        }
        Log::channel('products')->info('ProductService: Fetch single product', [
            'product_id' => $product->id
        ]);

        return $product->load('category');
    }
}

