<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from_city_id' => ['sometimes', 'exists:cities,id'],
            'to_city_id' => ['sometimes', 'exists:cities,id'],
            'departure_date' => ['sometimes', 'date'],
            'arrival_date' => ['sometimes', 'date', 'after_or_equal:departure_date'],
            'weight_available' => ['sometimes', 'numeric', 'min:0.1'],
            'weight_price' => ['sometimes', 'string', 'max:255'],
            'airline' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
