<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
public function store(Request $request)
{

    if($request->has('name')){
        echo "Name exists <br>";
    }

    if($request->filled('description')){
        echo "Description filled <br>";
    }

    dd(
$request->input('name'),
$request->price,
$request->all(),
$request->has('category'),
$request->filled('description')
);

    dump($request);
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
    public function update(Request $request, string $id, Product $product)
    {
        $product->update($request->all());

        return redirect()->route('products.index')->with('success', 'Product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully');
    }


    public function viewE()
    {
        return view('products.view');
    }

    public function jsonE()
    {
        return response()->json([
            'name' => "Digvijaysinh",
            'salary' => 12000,
            "pos" => 'PHP'
        ]);
    }

    public function redirectE()
    {
        return redirect()->route('products.index');
    }

    public function downloadE()
    {
        return response()->download(storage_path('app/test.txt'));
    }

    public function macroE()
    {
        return response()->success("Product created successfully");
    }
}
