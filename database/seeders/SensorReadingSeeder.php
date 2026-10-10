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

        $pollutants = [
            SensorReadingModel::TYPE_PM10 => 1000,
            SensorReadingModel::TYPE_SO2 => 450,
            SensorReadingModel::TYPE_CO => 10000,
            SensorReadingModel::TYPE_O3 => 200,
            SensorReadingModel::TYPE_NO2 => 46,
        ];

        $pollutantRows = [];
        foreach ($pollutants as $type => $value) {
            $pollutantRows[] = [
                'device_code' => 'ESP32-KOR1-DIORAMA',
                'koridor_id' => '1',
                'sensor_type' => $type,
                'value' => $value,
                'unit' => SensorReadingModel::defaultUnitFor($type),
                'recorded_at' => $now->copy()->subHours(2),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        SensorReadingModel::insert([
            ...$pollutantRows,
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
