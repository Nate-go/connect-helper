<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmailScheduleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'started_at' => $this->started_at,
            'after_second' => $this->after_second,
            'nextTime_at' => $this->nextTime_at,
            'sendMail' => $this->sendMail,
            'contacts' => $this->sendMail->contacts,
        ];

        return $data;
    }
}
