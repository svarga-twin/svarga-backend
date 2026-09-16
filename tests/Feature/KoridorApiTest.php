<?php

namespace Tests\Feature;

use App\Models\GreenSpaceModel;
use App\Models\KoridorModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KoridorApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_daftar_koridor_bisa_diambil(): void
    {
        KoridorModel::create([
            'short_name' => 'RTH Sritanjung', 'formal_name' => 'Koridor 1: Sritanjung',
            'thumbnail' => 'images/koridor/thumb-sritanjung.png',
            'hijau_level' => 'paling_hijau', 'hijau_score' => 5, 'status' => 'pilot',
            'shade_score' => 82,
        ]);

        $response = $this->getJson('/api/koridors');

        $response->assertStatus(200)->assertJsonCount(1, 'data');
    }

    public function test_detail_koridor_mengembalikan_url_gambar_absolut_dan_sensor_snapshot(): void
    {
        $koridor = KoridorModel::create([
            'short_name' => 'RTH Sritanjung', 'formal_name' => 'Koridor 1: Sritanjung',
            'thumbnail' => 'images/koridor/thumb-sritanjung.png',
            'hero_image' => 'images/koridor/hero-koridor1.png',
            'hijau_level' => 'paling_hijau', 'hijau_score' => 5, 'status' => 'pilot',
            'shade_score' => 82,
            'sensor_snapshot' => ['pm25' => 12, 'suhu' => 26.4, 'sensor_status' => 'online'],
        ]);

        $response = $this->getJson("/api/koridors/{$koridor->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.sensor.pm25', 12)
            ->assertJsonPath('data.sensor.suhu', 26.4);

        // Gambar harus jadi URL absolut (http://...), bukan path relatif mentah,
        // supaya <img src> di FE langsung bisa dipakai tanpa perlu digabung manual.
        $this->assertStringStartsWith('http', $response->json('data.thumbnail'));
    }

    public function test_koridor_yang_tidak_ada_mengembalikan_404(): void
    {
        $this->getJson('/api/koridors/999')->assertStatus(404);
    }

    public function test_daftar_green_space_bisa_diambil(): void
    {
        GreenSpaceModel::create([
            'name' => 'Taman Sritanjung', 'location_type' => 'taman',
            'latitude' => -8.21944, 'longitude' => 114.36972, 'shade_score' => 62,
        ]);

        $this->getJson('/api/green-spaces')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Taman Sritanjung');
    }
}
