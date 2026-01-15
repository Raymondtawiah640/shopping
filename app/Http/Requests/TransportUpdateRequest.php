<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransportUpdateRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'transport_type' => 'sometimes|string',
            'departure_location' => 'sometimes|string',
            'arrival_location' => 'sometimes|string',
            'price_per_person' => 'sometimes|numeric|min:0',
            'capacity' => 'sometimes|integer|min:1',
            'available_seats' => 'sometimes|integer|min:0',
            'departure_time' => 'sometimes|date',
            'arrival_time' => 'sometimes|date|after:departure_time',
            'images' => 'nullable',
            'is_active' => 'sometimes|boolean',
        ];
    }
}