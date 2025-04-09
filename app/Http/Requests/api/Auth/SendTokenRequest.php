<?php

namespace App\Http\Requests\api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SendTokenRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email|unique:users,email',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'O email é obrigatório',
            'email.email' => 'O email é inválido',
            'email.unique' => 'O email já está cadastrado',
        ];
    }
}