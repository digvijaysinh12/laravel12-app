<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only admin can update category
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:categories,name,' . $this->route('category')->id,
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please enter category name.',
            'name.unique'   => 'This category already exists.',
            'name.max'      => 'Category name must be less than 255 characters.',
        ];
    }
}