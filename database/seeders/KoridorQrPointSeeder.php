<?php

namespace Database\Seeders;

use App\Models\KoridorModel;
use App\Models\QrPoint;
use Illuminate\Database\Seeder;

/**
 * Seeder Titik Awal dan Titik Akhir QR Code untuk setiap koridor rute JEJAK WANGI.
 *
 * Mengambil koordinat titik awal ($path[0]) dan titik akhir ($path[last]) dari
 * masing-masing koridor, lalu membuat kode QR standar yang dapat dipindai oleh:
 *   1. Aplikasi warga / frontend (ScanPage di /scan dan /scan/akhir)
 *   2. Alur Check-in API (CheckinController @ /api/checkins/start & finish)
 *   3. Dashboard Admin (EngineController @ /api/admin/qr-points)
 *
 * Format kode QR murni angka:
 *   - Titik Awal : {id}01  (contoh: 101 untuk Koridor 1, 201 untuk Koridor 2, dst. Type: lapak)
 *   - Titik Akhir: {id}02  (contoh: 102 untuk Koridor 1, 202 untuk Koridor 2, dst. Type: tujuan)
 */
class KoridorQrPointSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan seeder Koridor sudah berjalan terlebih dahulu
        if (KoridorModel::count() === 0) {
            $this->call([GreenSpaceSeeder::class, KoridorSeeder::class]);
        }

        $koridors = KoridorModel::orderBy('id')->get();

        foreach ($koridors as $koridor) {
            $path = is_string($koridor->path) ? json_decode($koridor->path, true) : (array) ($koridor->path ?? []);
            if (empty($path)) {
                continue;
            }

            $startCoord = $path[0];
            $endCoord = $path[count($path) - 1];

            // Pisahkan label rute (mis. "Taman Sritanjung - Kampung Melayu")
            $labels = array_map('trim', explode('-', (string) $koridor->route_label));
            $startLabel = !empty($labels[0]) ? $labels[0] : $koridor->short_name;
            $endLabel = !empty($labels[1]) ? $labels[1] : 'Ujung ' . $koridor->short_name;

            // Kode murni angka: digit awal = ID koridor, digit akhir = 01 (awal) / 02 (akhir)
            $codeAwal = "{$koridor->id}01";
            $codeAkhir = "{$koridor->id}02";

            // 1. Titik Awal (Type: 'lapak' agar kompatibel dengan checkin /api/checkins/start)
            QrPoint::updateOrCreate(
                ['code' => $codeAwal],
                [
                    'name' => "Titik Awal Koridor {$koridor->id} ({$startLabel})",
                    'type' => 'lapak',
                    'koridor_id' => $koridor->id,
                    'lat' => $startCoord[0] ?? null,
                    'lng' => $startCoord[1] ?? null,
                    'is_active' => true,
                ]
            );

            // 2. Titik Akhir (Type: 'tujuan' agar kompatibel dengan checkin /api/checkins/{id}/finish)
            QrPoint::updateOrCreate(
                ['code' => $codeAkhir],
                [
                    'name' => "Titik Akhir Koridor {$koridor->id} ({$endLabel})",
                    'type' => 'tujuan',
                    'koridor_id' => $koridor->id,
                    'lat' => $endCoord[0] ?? null,
                    'lng' => $endCoord[1] ?? null,
                    'is_active' => true,
                ]
            );
        }
    }
}
