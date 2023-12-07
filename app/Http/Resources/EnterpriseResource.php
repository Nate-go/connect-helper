<?php

namespace App\Http\Resources;

use App\Constants\UserConstant\UserRole;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnterpriseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $owner = $this->users->first(function ($user) {
            return $user->role === UserRole::OWNER;
        });

        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'user' => [
                'id' => $owner->id,
                'name' => $owner->name,
                'image_url' => $owner->image_url,
                'email' => $owner->email,
            ],
            'users' => $this->users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'image_url' => $user->image_url,
                ];
            }),
        ];

        return $data;
    }
}
