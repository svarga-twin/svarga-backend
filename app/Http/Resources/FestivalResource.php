<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Bentuk JSON output disamakan dengan field di BwiFestPage.jsx
// (bfest_name, location_type, location_name, date).
class FestivalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'bfest_name' => $this->name,
            'location_type' => $this->location_type,
            'location_name' => $this->location_name,
            'date' => $this->event_date?->toDateString(),
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'description' => $this->description,
            'image' => $this->image_url,
            'is_active' => $this->is_active,
        ];
    }
}
