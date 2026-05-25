<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreBloodBagRequest extends FormRequest
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
            'refrigerator_id' => 'required|exists:refrigerators,id',
            'bag_number' => 'required|string|unique:blood_bags',
            'blood_group' => 'required|string',
            'donor_name' => 'required|string',
            'collection_date' => 'required|date',
            'expiry_date' => 'required|date|after:collection_date',
            'quantity' => 'required|integer|min:1',
            'status' => 'in:available,reserved,dispatched,expired',
        ];
    }

    /**
     * Custom error messages for validation.
     */
    public function messages(): array
    {
        return [
            'refrigerator_id.required' => 'Refrigerator ID is required.',
            'refrigerator_id.exists' => 'The selected refrigerator does not exist.',
            'bag_number.required' => 'Bag number is required.',
            'bag_number.unique' => 'This bag number is already registered.',
            'blood_group.required' => 'Blood group is required.',
            'donor_name.required' => 'Donor name is required.',
            'collection_date.required' => 'Collection date is required.',
            'expiry_date.required' => 'Expiry date is required.',
            'expiry_date.after' => 'Expiry date must be after the collection date.',
            'quantity.required' => 'Quantity is required.',
            'quantity.min' => 'Quantity must be at least 1.',
            'status.in' => 'Status must be one of: available, reserved, dispatched, expired.',
        ];
    }

    /**
     * Handle a failed validation attempt — return JSON for API.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
