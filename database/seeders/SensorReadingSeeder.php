<?php

namespace Database\Seeders;

use App\Models\SensorReadingModel;
use Illuminate\Database\Seeder;

// Sedikit riwayat pembacaan supaya GET /api/sensors/latest tidak kosong
// sebelum ESP32/simulator pernah mengirim data sama sekali.
class SensorReadingSeeder extends Seeder
{
    public function run(): void
    {
        SensorReadingModel::query()->delete(); // idempotent

        $now = now();

        SensorReadingModel::insert([
            [
                'device_code' => 'ESP32-KOR1-DIORAMA',
                'koridor_id' => '1',
                'sensor_type' => SensorReadingModel::TYPE_TEMPERATURE,
                'value' => 29.4,
                'unit' => '°C',
                'recorded_at' => $now->copy()->subHours(2),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'device_code' => 'ESP32-KOR1-DIORAMA',
                'koridor_id' => '1',
                'sensor_type' => SensorReadingModel::TYPE_AIR_QUALITY,
                'value' => 42,
                'unit' => 'AQI',
                'recorded_at' => $now->copy()->subHours(2),
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
