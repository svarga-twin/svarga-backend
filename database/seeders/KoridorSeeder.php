<?php

namespace Database\Seeders;

use App\Models\KoridorModel;
use Illuminate\Database\Seeder;

// Data disamakan persis dengan koridors[] di
// svarga-app/src/data/mockGreenSpaces.js — termasuk sensor_snapshot, supaya
// perilaku FE (termasuk kartu "Kondisi Lingkungan" di HomePage) tidak
// berubah saat berpindah dari mock/Firestore ke API ini. Gambar (thumbnail/
// hero_image) menunjuk ke public/images/koridor/ (lihat KoridorResource
// untuk cara jadi URL absolut).
class KoridorSeeder extends Seeder
{
    public function run(): void
    {
        KoridorModel::query()->delete();

        $rows = [
            [
                'id' => 1,
                'green_space_id' => 1,
                'short_name' => 'RTH Sritanjung',
                'formal_name' => 'Koridor 1: Sritanjung',
                'route_label' => 'Taman Sritanjung - Kampung Melayu',
                'desc' => 'Taman Sritanjung asri dan kuliner',
                'thumbnail' => 'images/koridor/thumb-sritanjung.png',
                'hero_image' => 'images/koridor/hero-koridor1.png',
                'hijau_level' => 'paling_hijau',
                'hijau_score' => 5,
                'umkm_count' => 5,
                'status' => 'pilot',
                'distance_meter' => 400,
                'estimate_minutes' => 6,
                'shade_score' => 82,
                'condition_title' => 'Kondisi Koridor Terpilih',
                'condition_desc' => 'Koridor ini memiliki cakupan pohon yang rimbun dan kualitas udara paling bersih di pusat kota. Dilengkapi infrastruktur pedestrian teduh.',
                'sensor' => ['pm25' => 12, 'pm25_status' => 'Sehat', 'suhu' => 26.4, 'suhu_status' => 'Sejuk', 'kelembaban' => 65, 'kelembaban_status' => 'Nyaman', 'kebisingan' => 52, 'kebisingan_status' => 'Tenang', 'uv_index' => 2.1, 'uv_status' => 'Rendah', 'sensor_status' => 'online'],
            ],
            [
                'id' => 2,
                'green_space_id' => null,
                'short_name' => 'Corridor 1 Kalilo',
                'formal_name' => 'Koridor 2: Kalilo',
                'route_label' => 'Kalilo - Jembatan Merah',
                'desc' => 'Kawasan sungai dan spot foto',
                'thumbnail' => 'images/koridor/thumb-kalilo.png',
                'hero_image' => 'images/koridor/thumb-kalilo.png',
                'hijau_level' => 'agak_hijau',
                'hijau_score' => 4,
                'umkm_count' => 8,
                'status' => 'rencana',
                'distance_meter' => 620,
                'estimate_minutes' => 9,
                'shade_score' => 64,
                'condition_title' => 'Kondisi Koridor Terpilih',
                'condition_desc' => 'Koridor menyusuri tepi sungai dengan pepohonan sedang. Ramai dikunjungi sore hari untuk berfoto dan bersantai.',
                'sensor' => ['pm25' => 24, 'pm25_status' => 'Sedang', 'suhu' => 28.1, 'suhu_status' => 'Hangat', 'kelembaban' => 58, 'kelembaban_status' => 'Nyaman', 'kebisingan' => 61, 'kebisingan_status' => 'Ramai', 'uv_index' => 4.3, 'uv_status' => 'Sedang', 'sensor_status' => 'online'],
            ],
            [
                'id' => 3,
                'green_space_id' => null,
                'short_name' => 'Corridor 2 Perliman',
                'formal_name' => 'Koridor 3: Perliman',
                'route_label' => 'Simpang Lima - Terminal',
                'desc' => 'Kawasan padat lalu lintas perkotaan',
                'thumbnail' => 'images/koridor/thumb-perliman.png',
                'hero_image' => 'images/koridor/thumb-perliman.png',
                'hijau_level' => 'setengah_hijau',
                'hijau_score' => 3,
                'umkm_count' => 3,
                'status' => 'rencana',
                'distance_meter' => 510,
                'estimate_minutes' => 8,
                'shade_score' => 41,
                'condition_title' => 'Kondisi Koridor Terpilih',
                'condition_desc' => 'Koridor padat lalu lintas dengan pohon peneduh terbatas. Disarankan berjalan pada jam bukan puncak.',
                'sensor' => ['pm25' => 38, 'pm25_status' => 'Sedang', 'suhu' => 30.2, 'suhu_status' => 'Panas', 'kelembaban' => 52, 'kelembaban_status' => 'Kering', 'kebisingan' => 74, 'kebisingan_status' => 'Bising', 'uv_index' => 6.8, 'uv_status' => 'Tinggi', 'sensor_status' => 'online'],
            ],
            [
                'id' => 4,
                'green_space_id' => 2,
                'short_name' => 'Koridor 3 Blambangan',
                'formal_name' => 'Koridor 4: Blambangan',
                'route_label' => 'Taman Blambangan - GOR',
                'desc' => 'Kawasan hijau dan pusat kuliner',
                'thumbnail' => 'images/koridor/thumb-blambangan.png',
                'hero_image' => 'images/koridor/thumb-blambangan.png',
                'hijau_level' => 'paling_hijau',
                'hijau_score' => 5,
                'umkm_count' => 6,
                'status' => 'rencana',
                'distance_meter' => 350,
                'estimate_minutes' => 5,
                'shade_score' => 79,
                'condition_title' => 'Kondisi Koridor Terpilih',
                'condition_desc' => 'Kawasan taman kota dengan kanopi pohon luas dan banyak pilihan kuliner di sekelilingnya.',
                'sensor' => ['pm25' => 15, 'pm25_status' => 'Sehat', 'suhu' => 27.0, 'suhu_status' => 'Sejuk', 'kelembaban' => 63, 'kelembaban_status' => 'Nyaman', 'kebisingan' => 55, 'kebisingan_status' => 'Tenang', 'uv_index' => 2.8, 'uv_status' => 'Rendah', 'sensor_status' => 'online'],
            ],
            [
                'id' => 5,
                'green_space_id' => null,
                'short_name' => 'Corridor 4 Pecinan',
                'formal_name' => 'Koridor 5: Pecinan',
                'route_label' => 'Klenteng - Pasar Lama',
                'desc' => 'Kawasan padat industri dan permukiman',
                'thumbnail' => 'images/koridor/thumb-pecinan.png',
                'hero_image' => 'images/koridor/thumb-pecinan.png',
                'hijau_level' => 'tidak_hijau',
                'hijau_score' => 1,
                'umkm_count' => 4,
                'status' => 'rencana',
                'distance_meter' => 480,
                'estimate_minutes' => 7,
                'shade_score' => 22,
                'condition_title' => 'Kondisi Koridor Terpilih',
                'condition_desc' => 'Kawasan padat bangunan dengan sedikit vegetasi. Kualitas udara perlu dipantau lebih ketat pada jam sibuk.',
                'sensor' => ['pm25' => 52, 'pm25_status' => 'Tidak Sehat', 'suhu' => 31.5, 'suhu_status' => 'Panas', 'kelembaban' => 48, 'kelembaban_status' => 'Kering', 'kebisingan' => 79, 'kebisingan_status' => 'Bising', 'uv_index' => 7.5, 'uv_status' => 'Tinggi', 'sensor_status' => 'online'],
            ],
            [
                'id' => 6,
                'green_space_id' => null,
                'short_name' => 'Corridor 5 Marina Boom',
                'formal_name' => 'Koridor 6: Marina Boom',
                'route_label' => 'Pelabuhan - Pantai Boom',
                'desc' => 'Kawasan padat industri dan permukiman',
                'thumbnail' => 'images/koridor/thumb-marinaboom.png',
                'hero_image' => 'images/koridor/thumb-marinaboom.png',
                'hijau_level' => 'agak_hijau',
                'hijau_score' => 4,
                'umkm_count' => 4,
                'status' => 'rencana',
                'distance_meter' => 610,
                'estimate_minutes' => 9,
                'shade_score' => 60,
                'condition_title' => 'Kondisi Koridor Terpilih',
                'condition_desc' => 'Koridor pesisir dengan angin laut sejuk. Vegetasi peneduh sedang, cocok untuk jalan sore.',
                'sensor' => ['pm25' => 20, 'pm25_status' => 'Sehat', 'suhu' => 28.6, 'suhu_status' => 'Hangat', 'kelembaban' => 70, 'kelembaban_status' => 'Lembab', 'kebisingan' => 58, 'kebisingan_status' => 'Tenang', 'uv_index' => 5.1, 'uv_status' => 'Sedang', 'sensor_status' => 'online'],
            ],
        ];

        foreach ($rows as $row) {
            $sensor = $row['sensor'];
            $sensor['recorded_at'] = now()->toIso8601String();
            unset($row['sensor']);
            $row['sensor_snapshot'] = json_encode($sensor);
            $row['created_at'] = now();
            $row['updated_at'] = now();
            KoridorModel::insert($row);
        }
    }
}
