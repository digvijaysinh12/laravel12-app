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
        Log::info('Reached controller: ProductController@index');

        Log::channel('products')->info('Controller: Product index');

        $result = $this->productService->getAllProducts($request);

        $products = $result['products'];

        return view('products.index',[
            'products' => $result['products'],
            'total_products' => $result['total'],
            'page_title' => 'Product Lit'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();

        Log::channel('products')->info('Create product page opened', [
            'user_id' => auth()->id()
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

            Log::channel('products')->info('Controller: Store product');

            $data = $request->validated();

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products', 'public');
                $data['image'] = $imagePath;
            }

            $this->productService->createProduct($data);

            return redirect()->route('products.index')
                    ->with('success','Product created successfully');

        }
    

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {

        Log::channel('products')->info('Controller: Show product');

        $product= $this->productService->getProduct($product);

        return view('products.show', compact('product'));
    }


    /**
     * Show the form for editing the specified resource.
     */

    public function edit(Product $product)
    {
        Log::info('Controller: Edit page');

        $categories = Category::all();

        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        try {

            Log::info('Controller: Update product');

            $data = $request->validated();
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            $this->productService->updateProduct($data, $product);

            return redirect()->route('products.index')
                ->with('success', 'Product updated successfully');

        } catch (Exception $e) {

            Log::channel('products')->error('Controller: Update failed', [
                'error' => $e->getMessage()
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
            Log::channel('products')->warning('Controller: Delete product');

            $this->productService->deleteProduct($product);

            return redirect()->route('products.index')
                ->with('success', 'Product deleted successfully');

        } catch (Exception $e) {

            Log::channel('products')->error('Controller: Delete failed', [
                'error' => $e->getMessage()
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
