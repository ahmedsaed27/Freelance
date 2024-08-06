<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status ?? "Pending",
            'date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'hours' => $this->hours,
            'is_paid' => $this->is_paid ?? false,
            'profile' => new ProfileResource($this->whenLoaded('profile')),
            'reservationHolder' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
