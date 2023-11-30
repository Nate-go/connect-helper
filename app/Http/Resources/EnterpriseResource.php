<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnterpriseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'user' => [
                'id' => $this->user()->id,
                'name' => $this->user()->name,
                'image_url' => $this->user()->image_url,
                'email' => $this->user()->email
            ],
            'users' => $this->users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'image_url' => $user->image_url
                ];
            })
        ];
        return $data;
    }
}
