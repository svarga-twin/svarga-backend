<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Bentuk JSON disamakan dengan field yang sudah dipakai sensorService.js
// di svarga-app (suhu/kualitas_udara ditampilkan sebagai `value` + `unit`
// generik, supaya satu bentuk resource bisa dipakai untuk kedua jenis sensor).
class SensorReadingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'device_code' => $this->device_code,
            'koridor_id' => $this->koridor_id,
            'sensor_type' => $this->sensor_type,
            'value' => (float) $this->value,
            'unit' => $this->unit,
            'recorded_at' => $this->recorded_at?->toIso8601String(),
        ];
    }
}
