<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from_city_id' => ['required', 'exists:cities,id'],
            'to_city_id' => ['required', 'exists:cities,id', 'different:from_city_id'],
            'departure_date' => ['required', 'date', 'after_or_equal:today'],
            'arrival_date' => ['required', 'date', 'after_or_equal:departure_date'],
            'weight_available' => ['required', 'numeric', 'min:0.1'],
            'weight_price' => ['required', 'string', 'max:255'],
            'airline' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
