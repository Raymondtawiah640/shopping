<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HospitalityBookingCreateRequest extends FormRequest
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
            'service_type' => 'required|in:hotel,restaurant,transport,tour',
            'service_id' => 'required|integer',
            'number_of_guests' => 'required|integer|min:1',
            'check_in_date' => 'nullable|date|after:today',
            'check_out_date' => 'nullable|date|after:check_in_date',
            'booking_date' => 'nullable|date|after:today',
            'special_requests' => 'nullable|string|max:1000',
        ];
    }
}