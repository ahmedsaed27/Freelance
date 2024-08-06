<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'is_visible' => $this->is_visible,
            'type_id' => $this->type_id,
            'title' => $this->title,
            'specialization' => $this->specialization,
            'max_amount' => $this->max_amount,
            'min_amount' => $this->min_amount,
            'description' => $this->description,
            'status' => $this->status,
            'country' => new CountriesResource($this->whenLoaded('country')),
            'city' => new CitiesResource($this->whenLoaded('city')),
            'currency' => new CurrencyResource($this->whenLoaded('currency')),
            'user' => !$this->is_anonymous ? new UserResource($this->whenLoaded('user')) : 'anonymous',
            'receive'=> $this->whenLoaded('receive'),
        ];
    }
}
