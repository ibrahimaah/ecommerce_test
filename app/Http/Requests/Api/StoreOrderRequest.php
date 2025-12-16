<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authentication is handled by middleware
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Order must contain at least one item.',
            'items.array' => 'Items must be an array.',
            'items.min' => 'Order must contain at least one item.',
            'items.*.product_id.required' => 'Each item must have a product ID.',
            'items.*.product_id.exists' => 'Selected product does not exist.',
            'items.*.quantity.required' => 'Each item must have a quantity.',
            'items.*.quantity.integer' => 'Quantity must be a valid integer.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'code' => 422,
            'status' => false,
            'message' => $validator->errors()->first(),
            'data' => null,
        ], 422));
    }
}
