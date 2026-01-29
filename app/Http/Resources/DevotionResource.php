<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DevotionResource extends JsonResource
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
            'content' => $this->content,
            'author' => $this->author,
            'image' => $this->image ? asset('storage/'.$this->image) : null,
            'key_verse' => $this->key_verse,
            'verses' => $this->verses,
            'date' => $this->date,
            'application_note' => $this->application_note,
            'prayer_note' => $this->prayer_note,
            'subheading' => $this->subheading,
            'status' => $this->status,
            'published_at' => $this->published_at,
            'created_by' => $this->created_by,
            'published_by' => $this->published_by,
        ];
    }
}
