<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SusbcriptionResource extends JsonResource
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
            'name' => $this->name,
            'price' => $this->price,
            'interval' => $this->interval,
            'plan_id' => $this->plan_id,
            'features' => $this->features,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
