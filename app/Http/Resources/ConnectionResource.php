<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConnectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'note' => $this->note,
            'type' => $this->type,
            'status' => $this->status,
            'owner' => $this->user?->name,
            'created_at' => $this->created_at,
            'tags' => $this->tags->where('user_id', auth()->user()->id)->pluck('name','id')->toArray(),
            'users' => $this->users->pluck('name')->toArray(),
        ];

        return $data;
    }
}
