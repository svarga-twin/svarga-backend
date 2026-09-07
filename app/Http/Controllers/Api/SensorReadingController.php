<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SensorReadingResource;
use App\Models\SensorReadingModel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

/**
 * Uji coba BE untuk inputan sensor (arahan mentor, 7 Sep 2026):
 *   1. Sensor suhu
 *   2. Sensor kualitas udara
 *
 * Alur yang diminta: data dari sensor -> langsung tampil di FE -> baru
 * disimpan ke DB. Tiga endpoint disediakan untuk mendukung alur itu:
 *
 *   POST /api/sensors/readings   Diperiksa dulu (validasi), payload yang
 *                                 sudah bersih dipakai sebagai bentuk
 *                                 balasan yang langsung bisa dirender FE
 *                                 (echo real-time), BARU kemudian
 *                                 disimpan lewat SensorReadingModel::create().
 *                                 Dipanggil oleh ESP32 (atau simulator saat
 *                                 hardware belum tersedia).
 *
 *   GET  /api/sensors/live       "Konsumsi langsung": hanya mengembalikan
 *                                 baris jika umurnya < 5 menit (FRESHNESS
 *                                 WINDOW). Dipakai FE untuk polling normal.
 *
 *   GET  /api/sensors/latest     Fallback dari DB: selalu mengembalikan
 *                                 baris terakhir yang tersimpan, berapa pun
 *                                 umurnya, plus flag is_stale. Dipakai FE
 *                                 kalau /live tidak mendapat data segar
 *                                 dalam 5 menit terakhir (lihat
 *                                 src/hooks/useSensorReading.js di svarga-app).
 */
class SensorReadingController extends Controller
{
    /** Jendela "data langsung"/segar, dalam menit, sesuai arahan mentor. */
    public const FRESHNESS_WINDOW_MINUTES = 5;

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_code' => ['required', 'string', 'max:100'],
            'koridor_id' => ['nullable', 'string', 'max:50'],
            'sensor_type' => ['required', 'string', 'in:' . implode(',', SensorReadingModel::TYPES)],
            'value' => ['required', 'numeric'],
            'unit' => ['nullable', 'string', 'max:20'],
            'recorded_at' => ['nullable', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Data sensor tidak valid', 'errors' => $validator->errors()], 422);
        }

        $payload = $validator->validated();
        $payload['unit'] = $payload['unit'] ?? SensorReadingModel::defaultUnitFor($payload['sensor_type']);
        $payload['recorded_at'] = $payload['recorded_at'] ?? now();

        // 1) Bentuk dulu objek bacaan yang siap dipakai FE (belum menyentuh DB
        //    sama sekali) — ini yang dimaksud "langsung tampil di FE": FE bisa
        //    memakai persis field yang sama dari response POST ini tanpa perlu
        //    query ulang, karena bentuknya sudah final di sini.
        $reading = new SensorReadingModel($payload);

        // 2) Baru simpan ke database setelah bentuk balasannya siap.
        $reading->save();

        return response()->json([
            'message' => 'Data sensor diterima',
            'data' => new SensorReadingResource($reading),
        ], 201);
    }

    public function live(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sensor_type' => ['required', 'string', 'in:' . implode(',', SensorReadingModel::TYPES)],
            'koridor_id' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Parameter tidak valid', 'errors' => $validator->errors()], 422);
        }

        $latest = SensorReadingModel::query()
            ->type($request->query('sensor_type'))
            ->koridor($request->query('koridor_id'))
            ->latest('recorded_at')
            ->first();

        $isFresh = $latest && $latest->recorded_at->gt(Carbon::now()->subMinutes(self::FRESHNESS_WINDOW_MINUTES));

        if (!$isFresh) {
            // Tidak ada data yang dikonsumsi "langsung" dalam 5 menit terakhir.
            // FE diharapkan fallback ke GET /api/sensors/latest (lihat useSensorReading.js).
            return response()->json([
                'data' => null,
                'is_stale' => true,
                'freshness_window_minutes' => self::FRESHNESS_WINDOW_MINUTES,
            ], 200);
        }

        return response()->json([
            'data' => new SensorReadingResource($latest),
            'is_stale' => false,
            'freshness_window_minutes' => self::FRESHNESS_WINDOW_MINUTES,
        ], 200);
    }

    public function latest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sensor_type' => ['required', 'string', 'in:' . implode(',', SensorReadingModel::TYPES)],
            'koridor_id' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Parameter tidak valid', 'errors' => $validator->errors()], 422);
        }

        $latest = SensorReadingModel::query()
            ->type($request->query('sensor_type'))
            ->koridor($request->query('koridor_id'))
            ->latest('recorded_at')
            ->first();

        if (!$latest) {
            return response()->json(['data' => null, 'is_stale' => true], 200);
        }

        $isStale = $latest->recorded_at->lte(Carbon::now()->subMinutes(self::FRESHNESS_WINDOW_MINUTES));

        return response()->json([
            'data' => new SensorReadingResource($latest),
            'is_stale' => $isStale,
            'source' => 'db_last_known',
        ], 200);
    }
}
