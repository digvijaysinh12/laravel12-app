<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $products = Product::all();

    //     return view('products.index', compact('products'));
    // }
public function index()
{
    return response()->json(Product::all());
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

        return[
            'category' => $category,
            'price' => $price
        ];
    }
    // public function store(Request $request)
    // {
    //     $validate = $request->validate([
    //         'name' => 'required',
    //         'price' => 'required|numeric',
    //         'description' => 'max:500',
    //         'category' => 'required'
    //     ]);

    //     Product::create($validate);

    //     return redirect()->route('product.index')->with('success','Product created successfully');
    // }

public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required',
        'price' => 'required|numeric',
        'description' => 'max:500',
        'category' => 'required'
    ]);

    $product = Product::create($validated);

    return response()->json($product);
}

    /**
     * Display the specified resource.
     */
    // public function show(Product $product)
    // {
    //     return view('products.show', compact('product'));
    // }
public function show(Product $product)
{
    return response()->json($product);
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
    // public function update(Request $request, string $id, Product $product)
    // {
    //     $validated = $request->validate([
    //         'name' =>'required',
    //         'price' => 'required|numeric',
    //         'description' => 'max:500',
    //         'category' => 'required'
    //     ]);

    //     $product->update($validated);

    //     return redirect()->route('product.index')->with('success', 'Product updated successfully');
    // }
    public function update(Request $request, Product $product)
{
    $product->update($request->all());

    return response()->json($product);
}

    /**
     * Remove the specified resource from storage.
     */
public function destroy(Product $product)
{
    $product->delete();

    return response()->json(['message' => 'Deleted successfully']);
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
