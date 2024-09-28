<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'price' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:0'],
        ];
    }

    /**
     * Custom messages for validation errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'price.required' => 'Fiyat alanı gereklidir.',
            'price.numeric' => 'Fiyat alanı sayısal bir değer olmalıdır.',
            'price.min' => 'Fiyat negatif olamaz.',
            'quantity.required' => 'Stok miktarı gereklidir.',
            'quantity.integer' => 'Stok miktarı tam sayı olmalıdır.',
            'quantity.min' => 'Stok miktarı negatif olamaz.',
        ];
    }
}
