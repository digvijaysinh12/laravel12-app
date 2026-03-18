<?php
    namespace App\Services;
    use App\Models\Product;

    class ProductService
    {
        public function store(array $data){
            return Product::create($data);
        }

        public function update(array $data, Product $product){
            $product->update($data);
            return $product->fresh();
        }

        public function delete(Product $product){
            return $product->delete();
        }
    }

