<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateBloodBagRequest extends FormRequest
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
     * All fields are optional on update (partial updates allowed).
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $bloodBagId = $this->route('blood_bag') ? $this->route('blood_bag')->id : null;

        return [
            'refrigerator_id' => 'sometimes|exists:refrigerators,id',
            'bag_number' => 'sometimes|string|unique:blood_bags,bag_number,' . $bloodBagId,
            'blood_group' => 'sometimes|string',
            'donor_name' => 'sometimes|string',
            'collection_date' => 'sometimes|date',
            'expiry_date' => 'sometimes|date|after:collection_date',
            'quantity' => 'sometimes|integer|min:1',
            'status' => 'sometimes|in:available,reserved,dispatched,expired',
        ];
    }

    /**
     * Custom error messages for validation.
     */
    public function messages(): array
    {
        return [
            'refrigerator_id.exists' => 'The selected refrigerator does not exist.',
            'bag_number.unique' => 'This bag number is already registered.',
            'expiry_date.after' => 'Expiry date must be after the collection date.',
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
