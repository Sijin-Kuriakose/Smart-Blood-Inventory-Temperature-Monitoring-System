<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTemperatureLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'refrigerator_id' => 'required|exists:refrigerators,id',
            'temperature' => 'required|numeric|between:-50,100',
            'recorded_at' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'refrigerator_id.required' => 'Refrigerator ID is required.',
            'refrigerator_id.exists' => 'The selected refrigerator does not exist.',
            'temperature.required' => 'Temperature is required.',
            'temperature.numeric' => 'Temperature must be a valid number.',
            'temperature.between' => 'Temperature must be between -50 and 100 °C.',
            'recorded_at.required' => 'Recording timestamp is required.',
            'recorded_at.date' => 'Recording timestamp must be a valid date.',
        ];
    }

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
