<?php

namespace App\Http\Requests\api\Auth\Route;

use Illuminate\Foundation\Http\FormRequest;

class RoutePointRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'created_at' => ['nullable', 'date:Y-m-d H:i:s'],
        ];
    }

    public function messages(): array
    {
        return [
            'latitude.required' => 'A latitude é obrigatória',
            'longitude.required' => 'A longitude é obrigatória',
            'latitude.numeric' => 'A latitude deve ser um número',
            'longitude.numeric' => 'A longitude deve ser um número',
            'created_at.date' => 'A data de criação deve ser uma data válida',
            'created_at.date:Y-m-d H:i:s' => 'A data de criação deve ser uma data válida no formato Y-m-d H:i:s',
        ];
    }
}
