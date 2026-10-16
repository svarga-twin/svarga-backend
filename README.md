# SVARGA Backend (Laravel API)

Backend API untuk proyek **SVARGA: Digital Twin & Wellness Corridor** (SMK
Negeri 1 Banyuwangi, tim Happy Fun Angkasa). Frontend utama (React, lihat
`../svarga-app`) memakai Firebase/Firestore untuk sebagian besar fitur;
backend Laravel ini menangani fitur-fitur spesifik yang lebih pas dipetakan
ke database relasional biasa:

| Fitur | Endpoint | Keterangan |
|---|---|---|
| UMKM sekitar koridor | `GET /api/umkm`, `GET /api/umkm/{id}` | Filter `?koridor_id=` |
| Kalender BWI-Fest | `GET /api/festivals`, `GET /api/festivals/{id}` | — |
| **Sensor ESP32** | `POST /api/sensors/readings`, `GET /api/sensors/snapshot`, `GET /api/sensors/history` | Lihat bagian Sensor ESP32 di bawah |

## Tech stack

- Laravel 13, PHP 8.3
- SQLite untuk development/testing (lihat `.env` / `phpunit.xml`); bisa
  diganti MySQL/PostgreSQL untuk produksi tanpa ubah kode (semua akses lewat
  Eloquent).

## Menjalankan

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan devices:register ESP32-KOR1-DIORAMA --koridor=1   # kunci API untuk ESP32
php artisan serve   # http://127.0.0.1:8000
```

Isi `VITE_LARAVEL_API_URL=http://127.0.0.1:8000/api` di `.env.local`
milik `svarga-app` agar frontend memakai API ini.

## Sensor ESP32 (suhu, kualitas udara, polutan ISPU)

Panduan lengkap + contoh firmware: [`docs/esp32/`](docs/esp32/README.md).

```
ESP32 --POST /api/sensors/readings (X-Device-Key)--> BE --> tabel sensor_readings
FE    --GET  /api/sensors/snapshot?koridor_id=1-----> semua jenis sensor, 1 request / 30 dtk
```

- **Auth per perangkat:** `php artisan devices:register <kode> --koridor=1` → kunci API (disimpan sebagai hash).
  `device_code`/`koridor_id` diambil dari perangkat, bukan payload.
- **Satu request = semua sensor**, ditulis dengan satu query INSERT. Throttle 20 request/menit per perangkat (429 + `Retry-After`).
- **Snapshot** memuat bacaan terakhir tiap jenis + `is_stale` (> 5 menit). Data lama tetap dikembalikan dari DB, jadi FE tidak butuh endpoint terpisah untuk fallback. Di-cache 15 detik dan otomatis dibersihkan saat ESP mengirim.
- `GET /api/sensors/history?sensor_type=pm10&days=7` — tren harian (dihitung di DB) untuk dashboard admin.
- `php artisan sensors:prune --days=30` — dijadwalkan harian lewat scheduler.

Jenis sensor: `temperature` (°C), `air_quality` (AQI), `pm10`, `so2`, `co`, `o3`, `no2` (semua **µg/m³**, CO juga). Jenis baru: tambah di `SensorReadingModel::TYPES`.

```bash
php artisan test --filter=SensorReadingApiTest
```

## Struktur relevan

```
app/Http/Controllers/Api/SensorReadingController.php   store / snapshot / history
app/Http/Middleware/AuthenticateDevice.php             X-Device-Key
app/Models/SensorReadingModel.php, SensorDeviceModel.php
app/Console/Commands/RegisterDevice.php, PruneSensorReadings.php
docs/esp32/                                            panduan + svarga_esp32.ino
```

Nama kolom pada model UMKM/Festival sengaja disamakan dengan field yang
sudah dipakai di `svarga-app/src/data/mockContent.js`, supaya React tidak
perlu banyak berubah saat pindah dari data mock/Firestore ke endpoint ini.
