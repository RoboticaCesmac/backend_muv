<?php

namespace App\Http\Requests\api\Auth\Route;

use Illuminate\Foundation\Http\FormRequest;

class RouteFinishRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
        ];
    }

    public function messages(): array
    {
        return [
            'latitude.required' => 'A latitude é obrigatória',
            'longitude.required' => 'A longitude é obrigatória',
            'latitude.numeric' => 'A latitude deve ser um número',
            'longitude.numeric' => 'A longitude deve ser um número',
        ];
    }
}