<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'image_url' => $this->user->image_url,
                'email' => $this->user->email
            ],
            'title' => $this->title,
            'content' => $this->content,
            'place' => $this->place,
            'type' => $this->type,
            'status' => $this->status,
            'classification' => $this->classification,
            'started_at' => $this->started_at,
            'finished_at' => $this->finished_at,
            'contacts' => $this->contacts->map(function ($contact) {
                return [
                    'id' => $contact->id,
                    'title' => $contact->title,
                    'content' => $contact->content,
                    'connection' => [
                        'name' => $contact->connection->name,
                        'note' => $contact->connection->note
                    ]
                ];
            }),
            'users' => $this->users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'image_url' => $user->image_url
                ];
            })
        ];
        return $data;
    }
}
