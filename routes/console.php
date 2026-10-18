<?php

use Illuminate\Support\Facades\Schedule;

// Hapus bacaan sensor lama supaya tabel tidak membengkak (jalankan scheduler:
// `* * * * * php artisan schedule:run` di cron server).
Schedule::command('sensors:prune')->dailyAt('03:00');

// Cek sensor offline & AQI tinggi tiap 5 menit → catat ke notifikasi admin.
Schedule::command('svarga:monitor')->everyFiveMinutes();
