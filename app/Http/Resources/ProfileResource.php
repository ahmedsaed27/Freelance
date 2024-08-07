<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
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
            'user' => new UserResource($this->whenLoaded('user')),
            'location' => $this->location,
            'areas_of_expertise' => $this->areas_of_expertise,
            'hourly_rate' => $this->hourly_rate,
            'years_of_experience' => $this->years_of_experience,
            'career' => $this->career,
            'city' => new CitiesResource($this->whenLoaded('city')),
            'country' => new CountriesResource($this->whenLoaded('country')),
            'bookings' => BookingResource::collection($this->whenLoaded('bookings')),
            'field' => $this->field,
            'specialization' => $this->specialization,
            'experience' => $this->experience,
        ];
    }
}
