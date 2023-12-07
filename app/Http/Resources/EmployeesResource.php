<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeesResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'image_url' => $this->image_url,
            'phonenumber' => $this->phonenumber,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth,
        ];

        return $data;
    }
}
