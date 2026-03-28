<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'message' => $this->message,
            'is_ai' => $this->is_ai,
            'is_read' => $this->is_read,
            'read_at' => $this->read_at?->format('Y-m-d H:i:s'),
            'sender' => [
                'id' => $this->sender->id,
                'name' => $this->sender->name,
            ],
            'receiver' => $this->when($this->receiver_id, [
                'id' => $this->receiver?->id,
                'name' => $this->receiver?->name,
            ]),
            'doctor' => $this->when($this->doctor_id, [
                'id' => $this->doctor?->id,
                'name' => $this->doctor?->name,
                'specialization' => $this->doctor?->specialization,
            ]),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
