<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'facebook_profile' => ['nullable', 'string', 'max:255'],
            'timezone' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }
}
