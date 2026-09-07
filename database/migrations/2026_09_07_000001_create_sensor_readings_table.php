<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// SVARGA Backend (Laravel) — tabel sensor_readings.
//
// Uji coba alur sensor -> BE -> FE -> DB sesuai arahan mentor (7 Sep 2026):
//   1. Perangkat sensor (ESP32/simulator) mem-POST bacaan ke BE.
//   2. BE membentuk payload balasan dulu (langsung dipakai FE lewat response
//      request itu sendiri / lewat endpoint "live"), BARU kemudian
//      menyimpannya ke tabel ini — lihat SensorReadingController@store.
//   3. FE membaca data lewat dua endpoint: /api/sensors/live (data segar,
//      <5 menit) dan /api/sensors/latest (fallback: baris terakhir di DB,
//      berapa pun umurnya) — lihat catatan lengkap di README.md.
//
// Baru mendukung 2 jenis sensor sesuai permintaan uji coba: suhu & kualitas
// udara. Kolom sensor_type dibuat string (bukan enum kaku) supaya jenis
// sensor lain (kelembaban, UV, kebisingan, dst.) tinggal ditambah tanpa
// migration baru.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sensor_readings', function (Blueprint $table) {
            $table->id();
            $table->string('device_code'); // contoh: ESP32-SRT-01
            $table->string('koridor_id')->nullable(); // relasi longgar ke id koridor di svarga-app
            $table->string('sensor_type'); // 'temperature' | 'air_quality'
            $table->decimal('value', 8, 2); // suhu (°C) atau indeks kualitas udara (AQI)
            $table->string('unit')->nullable(); // '°C' | 'AQI'
            $table->timestamp('recorded_at'); // waktu pembacaan sensor (bukan waktu simpan ke DB)
            $table->timestamps();

            $table->index(['sensor_type', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sensor_readings');
    }
};
