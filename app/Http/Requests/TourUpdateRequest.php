<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TourUpdateRequest extends FormRequest
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
            'price_per_person' => 'sometimes|numeric|min:0',
            'duration_days' => 'sometimes|integer|min:1',
            'max_participants' => 'sometimes|integer|min:1',
            'available_spots' => 'sometimes|integer|min:0',
            'start_date' => 'sometimes|date|after:today',
            'end_date' => 'sometimes|date|after:start_date',
            'itinerary' => 'nullable',
            'images' => 'nullable',
            'is_active' => 'sometimes|boolean',
        ];
    }
}