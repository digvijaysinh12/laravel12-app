<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ManageOrderRequest extends FormRequest
{
    private const STATUS_OPTIONS = [
        'pending',
        'confirmed',
        'shipped',
        'delivered',
        'cancelled',
    ];

    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')->where('role', 'user')],
            'shipping_address' => ['required', 'string', 'max:1000'],
            'phone' => ['required', 'string', 'max:20'],
            'status' => ['required', Rule::in(self::STATUS_OPTIONS)],
            'payment_method' => ['required', 'string', 'max:100'],
            'payment_status' => ['required', 'string', 'max:100'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id', 'distinct'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
