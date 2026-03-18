<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use App\Facades\Product as ProductFacade;

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

    public function index()
    {
        $products = Product::all();

        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('products.create');
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
    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'description' => 'max:500',
            'category' => 'required'
        ]);

        $imagePath = $request->file('image')->store('products', 'public');

        $validate['image'] = $imagePath;

        $product = ProductFacade::store($validate);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully');
    }



    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'description' => 'max:500',
            'category' => 'required'
        ]);

        $product = ProductFacade::update($validated, $product);
        return redirect()->route('products.index')->with('success', 'Product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        ProductFacade::delete($product);
        return redirect()->route('products.index')->with('success', 'Product deleted successfully');
    }

}
