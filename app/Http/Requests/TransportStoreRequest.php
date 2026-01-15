<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransportStoreRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'transport_type' => 'required|string',
            'departure_location' => 'required|string',
            'arrival_location' => 'required|string',
            'price_per_person' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'available_seats' => 'required|integer|min:0',
            'departure_time' => 'required|date',
            'arrival_time' => 'required|date|after:departure_time',
            'images' => 'nullable',
        ];
    }
}