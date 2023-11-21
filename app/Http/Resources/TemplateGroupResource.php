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
            'name' => $this->name,
            'status' => $this->status,
            'user' => $this->user->name,
            'templates' => $this->publicTemplates->map(function ($template) {
                return [
                    'id' => $template->id,
                    'name' => $template->name,
                ];
            })
        ];
        return $data;
    }
}
