<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'price' => (float) $this->price,
            'location' => $this->whenLoaded('location', fn () => [
                'id' => $this->location->id,
                'city' => $this->location->city,
                'province' => $this->location->province,
            ]),
            'type' => $this->whenLoaded('type', fn () => [
                'id' => $this->type->id,
                'name' => $this->type->type_name,
            ]),
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'floor_area' => (float) $this->floor_area,
            'status' => $this->status,
            'image' => $this->whenLoaded('primaryImage', fn () => $this->primaryImage?->url),
            'images' => $this->whenLoaded('images', fn () => $this->images->map(fn ($i) => [
                'id' => $i->id,
                'url' => $i->url,
                'is_primary' => $i->is_primary,
            ])),
            'created_at' => $this->created_at,
        ];
    }
}
