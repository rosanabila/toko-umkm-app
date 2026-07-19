<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->isPenjual();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048', // Max 2MB image
            
            // Variants validation
            'variant_name.*' => 'nullable|string|max:100',
            'variant_price.*' => 'nullable|numeric|min:0',
            'variant_stock.*' => 'nullable|integer|min:0',
        ];
    }
}
