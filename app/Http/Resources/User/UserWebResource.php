<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserWebResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'is_admin' => $this->is_admin,
            'is_first_login' => $this->is_first_login,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'can_edit' => $this->canEdit(),
            'can_delete' => $this->canDelete(),
            'can_update' => $this->canUpdate(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}