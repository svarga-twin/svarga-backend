<?php

namespace Database\Seeders;

use App\Models\Umkm;
use Illuminate\Database\Seeder;

// Data disamakan persis dengan src/data/mockContent.js di svarga-app,
// supaya tampilan tidak berubah saat React pindah dari data mock ke API ini.
class UmkmSeeder extends Seeder
{
    public function run(): void
    {
        Umkm::query()->delete(); // aman dijalankan berkali-kali (idempotent)

        Umkm::insert([
            [
                'business_name' => 'Warung Bu Sari',
                'business_type' => 'Makanan',
                'address' => 'Sekitar Taman Sritanjung',
                'koridor_id' => '1',
                'distance_m' => 120,
                'rating' => 4.8,
                'image_url' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_name' => 'Es Dawet Mbak Tini',
                'business_type' => 'Minuman Tradisional',
                'address' => 'Sekitar Taman Sritanjung',
                'koridor_id' => '1',
                'distance_m' => 200,
                'rating' => 4.6,
                'image_url' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_name' => 'Rujak Soto Bu Ida',
                'business_type' => 'Makanan',
                'address' => 'Sekitar Taman Blambangan',
                'koridor_id' => '4',
                'distance_m' => 150,
                'rating' => 4.7,
                'image_url' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_name' => 'Batik Gajah Oling Corner',
                'business_type' => 'Kerajinan',
                'address' => 'Koridor 1',
                'koridor_id' => '1',
                'distance_m' => 90,
                'rating' => 4.5,
                'image_url' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
