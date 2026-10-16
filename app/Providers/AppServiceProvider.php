<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // ESP32 normalnya kirim tiap 30–60 detik; 20/menit per perangkat sudah longgar
        // dan melindungi DB dari firmware yang loop tanpa delay.
        RateLimiter::for('sensor-ingest', fn (Request $request) => Limit::perMinute(20)
            ->by($request->attributes->get('device')?->id ?? $request->ip()));
    }
}
