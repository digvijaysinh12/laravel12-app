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
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
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
