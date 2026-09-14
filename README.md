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
| **Sensor suhu & kualitas udara** (uji coba) | `POST /api/sensors/readings`, `GET /api/sensors/live`, `GET /api/sensors/latest` | Lihat bagian khusus di bawah |

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
php artisan serve   # http://127.0.0.1:8000
```

Isi `VITE_LARAVEL_API_URL=http://127.0.0.1:8000/api` di `.env.local`
milik `svarga-app` agar frontend memakai API ini.

## Uji coba input sensor (suhu & kualitas udara)

> Arahan mentor (7 Sep 2026): *"coba uji buat BE untuk inputan dari sensor:
> 1) sensor suhu, 2) sensor kualitas udara. Alurnya dari BE langsung tampil
> di FE, kemudian simpan di DB."*

### Alur

```
Sensor (ESP32 / simulator)
   │  POST /api/sensors/readings  { device_code, sensor_type, value }
   ▼
SensorReadingController@store
   │  1. Validasi payload
   │  2. Bentuk objek bacaan yang siap dipakai FE (tanpa query DB dulu)
   │  3. BARU simpan ke tabel sensor_readings
   ▼
Response 201 { data: { ...bacaan yang sama, sekarang punya id } }
```

FE (lihat `svarga-app/src/hooks/useSensorReading.js`) lalu membaca data lewat
dua endpoint terpisah:

- **`GET /api/sensors/live?sensor_type=temperature&koridor_id=1`** — hanya
  mengembalikan data kalau umurnya **< 5 menit** (`FRESHNESS_WINDOW_MINUTES`
  di `SensorReadingController`). Kalau tidak ada data segar:
  `{ "data": null, "is_stale": true }`.
- **`GET /api/sensors/latest?sensor_type=temperature&koridor_id=1`** —
  fallback: selalu mengembalikan baris terakhir yang tersimpan di DB, berapa
  pun umurnya, plus flag `is_stale`. Dipakai FE kalau `/live` tidak
  mendapat data segar selama 5 menit berturut-turut.

Jenis sensor yang didukung sejauh ini: `temperature` (°C) dan `air_quality`
(AQI) — sesuai dua sensor yang diminta untuk uji coba. Menambah jenis sensor
lain (kelembaban, UV, kebisingan) tidak perlu migration baru, cukup tambah
nilai baru di `SensorReadingModel::TYPES`.

### Simulasi sensor lewat curl

Setelah `php artisan serve` menyala:

```bash
# 1) Sensor suhu mengirim data
curl -X POST http://127.0.0.1:8000/api/sensors/readings \
  -H "Content-Type: application/json" \
  -d '{"device_code":"ESP32-SRT-01","koridor_id":"1","sensor_type":"temperature","value":31.7}'

# 2) Sensor kualitas udara mengirim data
curl -X POST http://127.0.0.1:8000/api/sensors/readings \
  -H "Content-Type: application/json" \
  -d '{"device_code":"ESP32-SRT-01","koridor_id":"1","sensor_type":"air_quality","value":48}'

# 3) FE membaca data langsung (harus fresh, is_stale: false)
curl "http://127.0.0.1:8000/api/sensors/live?sensor_type=temperature&koridor_id=1"

# 4) Setelah >5 menit tanpa data baru, /live akan is_stale: true —
#    FE lalu fallback ke data terakhir dari DB:
curl "http://127.0.0.1:8000/api/sensors/latest?sensor_type=temperature&koridor_id=1"
```

### Automated test

```bash
php artisan test --filter=SensorReadingApiTest
```

6 skenario diuji di `tests/Feature/SensorReadingApiTest.php`:

1. Sensor suhu berhasil kirim data → tersimpan di DB.
2. Sensor kualitas udara berhasil kirim data → tersimpan di DB.
3. `sensor_type` yang tidak dikenal ditolak (422).
4. `/live` mengembalikan data kalau masih < 5 menit.
5. `/live` menandai `is_stale: true` kalau sudah > 5 menit.
6. `/latest` tetap mengembalikan data (fallback DB) walau `/live` sudah stale.

## Struktur relevan

```
app/Http/Controllers/Api/
  ├── UmkmController.php
  ├── FestivalController.php
  └── SensorReadingController.php   ← store / live / latest
app/Models/
  ├── UmkmModel.php
  ├── FestivalModel.php
  └── SensorReadingModel.php
database/migrations/
  └── 2026_09_07_000001_create_sensor_readings_table.php
database/seeders/
  └── SensorReadingSeeder.php       ← contoh data awal, jalan otomatis lewat DatabaseSeeder
tests/Feature/
  └── SensorReadingApiTest.php
```

Nama kolom pada model UMKM/Festival sengaja disamakan dengan field yang
sudah dipakai di `svarga-app/src/data/mockContent.js`, supaya React tidak
perlu banyak berubah saat pindah dari data mock/Firestore ke endpoint ini.
