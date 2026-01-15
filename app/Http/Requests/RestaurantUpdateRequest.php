<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RestaurantUpdateRequest extends FormRequest
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
            'location' => 'sometimes|string',
            'city' => 'sometimes|string',
            'country' => 'sometimes|string',
            'cuisine_type' => 'sometimes|string',
            'average_price' => 'sometimes|numeric|min:0',
            'capacity' => 'sometimes|integer|min:1',
            'opening_hours' => 'nullable',
            'images' => 'nullable',
            'is_active' => 'sometimes|boolean',
        ];
    }
}