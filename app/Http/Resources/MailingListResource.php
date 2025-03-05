<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MailingListResource extends JsonResource
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
            'name' => $this->name ?? null,
            'type' => $this->type ?? null,
            'description' => $this->description ?? null,
            'status' => $this->status ?? null,
            'creation_date' => $this->creation_date ?? null,
            'last_updated_date' => $this->last_updated_date ?? null,
            'owner_id' => $this->owner_id ?? null,
            'tags' => $this->tags ?? null,
        ];
    }
}