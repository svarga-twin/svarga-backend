<?php

namespace Tests\Feature;

use App\Models\SensorReadingModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Uji coba BE untuk inputan sensor sesuai arahan mentor (7 Sep 2026):
 *   1. Sensor suhu
 *   2. Sensor kualitas udara
 * Alurnya: dari BE langsung tampil di FE, kemudian simpan ke DB.
 *
 * Jalankan: php artisan test --filter=SensorReadingApiTest
 */
class SensorReadingApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_sensor_suhu_dapat_mengirim_data_dan_langsung_tersimpan(): void
    {
        $response = $this->postJson('/api/sensors/readings', [
            'device_code' => 'ESP32-KOR1-DIORAMA',
            'koridor_id' => '1',
            'sensor_type' => 'temperature',
            'value' => 30.2,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.sensor_type', 'temperature')
            ->assertJsonPath('data.value', 30.2)
            ->assertJsonPath('data.unit', '°C');

        // Baris yang sama harus sudah tersimpan di DB (bukan cuma di response).
        $this->assertDatabaseHas('sensor_readings', [
            'device_code' => 'ESP32-KOR1-DIORAMA',
            'sensor_type' => 'temperature',
            'value' => 30.2,
        ]);
    }

    public function test_sensor_kualitas_udara_dapat_mengirim_data_dan_langsung_tersimpan(): void
    {
        $response = $this->postJson('/api/sensors/readings', [
            'device_code' => 'ESP32-KOR1-DIORAMA',
            'koridor_id' => '1',
            'sensor_type' => 'air_quality',
            'value' => 55,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.sensor_type', 'air_quality')
            ->assertJsonPath('data.unit', 'AQI');

        $this->assertDatabaseHas('sensor_readings', [
            'sensor_type' => 'air_quality',
            'value' => 55,
        ]);
    }

    public function test_reading_dengan_sensor_type_tidak_dikenal_ditolak(): void
    {
        $response = $this->postJson('/api/sensors/readings', [
            'device_code' => 'ESP32-KOR1-DIORAMA',
            'sensor_type' => 'kelembaban', // belum didukung di uji coba ini
            'value' => 60,
        ]);

        $response->assertStatus(422);
    }

    public function test_live_endpoint_mengembalikan_data_jika_masih_dalam_5_menit(): void
    {
        SensorReadingModel::create([
            'device_code' => 'ESP32-KOR1-DIORAMA',
            'koridor_id' => '1',
            'sensor_type' => 'temperature',
            'value' => 28.5,
            'unit' => '°C',
            'recorded_at' => now()->subMinutes(2),
        ]);

        $response = $this->getJson('/api/sensors/live?sensor_type=temperature&koridor_id=1');

        $response->assertStatus(200)
            ->assertJsonPath('is_stale', false)
            ->assertJsonPath('data.value', 28.5);
    }

    public function test_live_endpoint_menandai_stale_jika_lebih_dari_5_menit(): void
    {
        SensorReadingModel::create([
            'device_code' => 'ESP32-KOR1-DIORAMA',
            'koridor_id' => '1',
            'sensor_type' => 'temperature',
            'value' => 27.0,
            'unit' => '°C',
            'recorded_at' => now()->subMinutes(10),
        ]);

        $response = $this->getJson('/api/sensors/live?sensor_type=temperature&koridor_id=1');

        $response->assertStatus(200)
            ->assertJsonPath('is_stale', true)
            ->assertJsonPath('data', null);
    }

    public function test_latest_endpoint_selalu_mengembalikan_data_terakhir_dari_db_sebagai_fallback(): void
    {
        SensorReadingModel::create([
            'device_code' => 'ESP32-KOR1-DIORAMA',
            'koridor_id' => '1',
            'sensor_type' => 'air_quality',
            'value' => 61,
            'unit' => 'AQI',
            'recorded_at' => now()->subHours(3), // jauh lebih lama dari 5 menit
        ]);

        // /live harus sudah menyerah (stale)...
        $this->getJson('/api/sensors/live?sensor_type=air_quality&koridor_id=1')
            ->assertJsonPath('is_stale', true);

        // ...tapi /latest tetap mengembalikan baris terakhir sebagai fallback DB, dengan flag stale.
        $this->getJson('/api/sensors/latest?sensor_type=air_quality&koridor_id=1')
            ->assertStatus(200)
            ->assertJsonPath('data.value', 61)
            ->assertJsonPath('is_stale', true);
    }
}
