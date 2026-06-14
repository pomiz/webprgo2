<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'selected_products' => ['required', 'array', 'min:1'],
            'selected_products.*' => ['integer', 'exists:products,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'selected_products.required' => 'Pilih minimal satu produk untuk checkout.',
            'selected_products.min' => 'Pilih minimal satu produk untuk checkout.',
        ];
    }
}
