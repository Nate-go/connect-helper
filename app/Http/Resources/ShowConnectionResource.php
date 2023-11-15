<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowConnectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'note' => $this->note,
            'type' => $this->type,
            'status' => $this->status,
            'owner' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'image_url' => $this->user->image_url,
                'email' => $this->user->email
            ],
            'created_at' => $this->created_at,
            'tags' => $this->tags->where('user_id', auth()->user()->id)->map(function($tag) {
                return [
                    'id' => $tag->id,
                    'name'=> $tag->name,
                ];
            }),
            'users' => $this->users->map(function($user) {
                return [
                    'id' => $user->id,
                    'name'=> $user->name,
                    'image_url' => $user->image_url,
                    'email' => $user->email
                ];
            }),
        ];

        return $data;
    }
}
