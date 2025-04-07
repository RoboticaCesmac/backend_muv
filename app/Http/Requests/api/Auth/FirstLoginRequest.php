<?php

namespace App\Http\Requests\api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class FirstLoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'date_of_birth' => ['required', 'date:format:Y-m-d'],
            'gender' => ['required', 'string', 'in:male,female'],
            'user_name' => ['required', 'string', 'max:25'],
            'vehicle_id' => ['required', 'exists:vehicles,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'date_of_birth.required' => 'A data de nascimento é obrigatória.',
            'gender.required' => 'O gênero é obrigatório.',
            'gender.in' => 'O gênero deve ser male ou female.', 
            'user_name.required' => 'O nome de usuário é obrigatório.',
            'user_name.max' => 'O nome de usuário deve ter no máximo 25 caracteres.',
            'vehicle_id.required' => 'O veículo é obrigatório.',
            'vehicle_id.exists' => 'O veículo não existe.',
        ];
    }
}