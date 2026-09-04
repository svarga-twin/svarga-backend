<?php

namespace Database\Seeders;

use App\Models\FestivalModel;
use Illuminate\Database\Seeder;

// Data disamakan persis dengan src/data/mockContent.js (bfestEvents) di svarga-app.
class FestivalSeeder extends Seeder
{
    public function run(): void
    {
        FestivalModel::query()->delete(); // aman dijalankan berkali-kali (idempotent)

        FestivalModel::insert([
            [
                'name' => 'Banyuwangi Ethno Carnival',
                'location_type' => 'panggung',
                'location_name' => 'Taman Blambangan',
                'event_date' => '2026-09-12',
                'description' => 'Event kolosal menampilkan ratusan peraga busana kontemporer berbasis budaya adat yang ramah lingkungan.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Festival Gandrung Sewu',
                'location_type' => 'panggung',
                'location_name' => 'Pantai Boom',
                'event_date' => '2026-09-24',
                'description' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Festival Kuwung',
                'location_type' => 'panggung',
                'location_name' => 'Taman Blambangan',
                'event_date' => '2026-10-03',
                'description' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
