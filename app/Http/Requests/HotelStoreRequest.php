<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HotelStoreRequest extends FormRequest
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
            'location' => 'required|string',
            'city' => 'required|string',
            'country' => 'required|string',
            'price_per_night' => 'required|numeric|min:0',
            'total_rooms' => 'required|integer|min:1',
            'available_rooms' => 'required|integer|min:0',
            'amenities' => 'nullable|array',
            'images' => 'nullable|array',
        ];
    }
}