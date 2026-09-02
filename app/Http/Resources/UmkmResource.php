<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Bentuk JSON output disamakan dengan field yang sudah dipakai React
// (business_name, business_type, address, distance_m, rating, image)
// supaya UmkmPage.jsx tidak perlu banyak berubah saat pindah dari
// Firestore ke endpoint ini.
class UmkmResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'business_name' => $this->business_name,
            'business_type' => $this->business_type,
            'address' => $this->address,
            'koridor_id' => $this->koridor_id,
            'distance_m' => $this->distance_m,
            'rating' => $this->rating !== null ? (float) $this->rating : null,
            'image' => $this->image_url,
            'latitude' => $this->latitude !== null ? (float) $this->latitude : null,
            'longitude' => $this->longitude !== null ? (float) $this->longitude : null,
            'is_active' => $this->is_active,
        ];
    }
}
