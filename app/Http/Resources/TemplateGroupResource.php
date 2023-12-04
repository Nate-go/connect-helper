<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TemplateGroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'image_url' => $this->user->image_url,
                'email' => $this->user->email,
            ],
            'templates' => $this->publicTemplates->map(function ($template) {
                return [
                    'id' => $template->id,
                    'name' => $template->name,
                ];
            }),
        ];

        return $data;
    }
}
