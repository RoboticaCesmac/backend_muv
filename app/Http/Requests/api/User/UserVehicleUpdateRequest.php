<?php

namespace App\Http\Requests\api\User;

use Illuminate\Foundation\Http\FormRequest;

class UserVehicleUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'vehicle_id' => ['required', 'integer', 'exists:vehicles,id'],
        ];
    }
}
