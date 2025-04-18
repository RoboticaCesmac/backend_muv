<?php

namespace App\Http\Requests\api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{

    public function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'O email é obrigatório',
            'email.email' => 'O email é inválido',
            'password.required' => 'A senha é obrigatória',
            'password.min' => 'A senha deve conter pelo menos 8 caracteres',
            'password.confirmed' => 'As senhas não conferem',
        ];
    }
}
