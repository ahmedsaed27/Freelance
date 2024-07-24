<?php

namespace App\Http\Resources;

use App\Models\Document;
use App\Models\Paper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfilePaperResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $documentModel = Paper::findOrFail($this->papers_id);
        if ($documentModel->data_type == 'file') {
            $value = env("APP_URL") . "/uploads" . "/" . $this->value;
        } else {
            $value = $this->value;
        }
        return [
            'id' => $this->id,
            'value' => $value,
            'status' => $this->status ?? "Under Review",
            'profile' => new ProfileResource($this->whenLoaded('profile')),
            'paper' => new PaperResource($this->whenLoaded('papers')),
        ];
    }
}
