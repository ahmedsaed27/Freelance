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
            'id' => $this->id,
            'is_visible' => $this->is_visible,
            'type_id' => $this->type_id,
            'title' => $this->title,
            'specialization' => $this->specialization,
            'max_amount' => $this->max_amount,
            'min_amount' => $this->min_amount,
            'number_of_days' => $this->number_of_days,
            'description' => $this->description,
            'status' => $this->status,
            'country' => new CountriesResource($this->whenLoaded('country')),
            'city' => new CitiesResource($this->whenLoaded('city')),
            'currency' => new CurrencyResource($this->whenLoaded('currency')),
            'is_anonymous' => $this->is_anonymous,
            'user' => $this->is_anonymous == true ? 'anonymous' : new UserResource($this->whenLoaded('user')),
            'receive'=> $this->whenLoaded('receive'),
            'keywords'=> $this->whenLoaded('caseKeyword'),
            'Skills'=> $this->whenLoaded('caseSkill'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
