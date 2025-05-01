<?php

namespace App\Http\Requests\api\Auth\Route;

use Illuminate\Foundation\Http\FormRequest;

class RouteStartRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
        ];
    }

    public function messages(): array
    {
        return [
            'vehicle_id.required' => 'O veículo é obrigatório',
            'vehicle_id.exists' => 'O veículo não existe',
            'latitude.required' => 'A latitude é obrigatória',
            'longitude.required' => 'A longitude é obrigatória',
            'latitude.numeric' => 'A latitude deve ser um número',
            'longitude.numeric' => 'A longitude deve ser um número',
        ];
    }
}