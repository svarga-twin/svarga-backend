<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MoodLogModel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

/**
 * Menggantikan koleksi Firestore `mood_logs` + `mood_aggregates` (lihat
 * moodService.js di svarga-app). Data mood tetap agregat/anonim di
 * response (bukan tugas endpoint ini menyingkap identitas pengguna),
 * sesuai Batasan 4.1 proposal — mood_score individual memang tersimpan di
 * DB untuk keperluan agregasi, tapi endpoint publik hanya membuka
 * ringkasan (`summary`), tidak pernah daftar entri per-pengguna.
 */
class MoodLogController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'green_space_id' => ['required', 'integer'],
            'mood_score' => ['required', 'integer', 'min:1', 'max:4'],
            'activity' => ['nullable', 'string', 'max:50'],
            'note' => ['nullable', 'string', 'max:1000'],
            'anonymous_session_id' => ['nullable', 'string', 'max:100'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Data mood tidak valid', 'errors' => $validator->errors()], 422);
        }

        $payload = $validator->validated();
        $payload['user_id'] = $request->user('sanctum')?->id; // null kalau tamu (token tidak dikirim/tidak valid)
        $payload['logged_at'] = now();

        $log = MoodLogModel::create($payload);

        return response()->json(['message' => 'Mood tersimpan', 'data' => ['id' => $log->id]], 201);
    }

    /**
     * Ringkasan agregat untuk halaman Riwayat Mood: tren rata-rata per hari
     * selama N hari terakhir + distribusi skor mood. Presentasi (label,
     * emoji, warna) sengaja tetap jadi urusan FE (lihat MoodHistoryPage.jsx),
     * di sini hanya angka mentah.
     */
    public function summary(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'green_space_id' => ['required', 'integer'],
            'days' => ['nullable', 'integer', 'min:1', 'max:90'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Parameter tidak valid', 'errors' => $validator->errors()], 422);
        }

        $greenSpaceId = (int) $request->query('green_space_id');
        $days = (int) ($request->query('days') ?? 7);
        $since = Carbon::now()->subDays($days - 1)->startOfDay();

        $logs = MoodLogModel::query()
            ->where('green_space_id', $greenSpaceId)
            ->where('logged_at', '>=', $since)
            ->get(['mood_score', 'logged_at']);

        // Tren harian: rata-rata skor per tanggal, mengisi hari tanpa data dengan null
        // supaya grafik FE tetap punya sumbu-x yang lengkap (bukan bolong).
        $byDate = $logs->groupBy(fn ($log) => $log->logged_at->toDateString());

        $daily = [];
        for ($i = 0; $i < $days; $i++) {
            $date = $since->copy()->addDays($i)->toDateString();
            $dayLogs = $byDate->get($date, collect());
            $daily[] = [
                'date' => $date,
                'average_score' => $dayLogs->isEmpty() ? null : round($dayLogs->avg('mood_score'), 2),
                'count' => $dayLogs->count(),
            ];
        }

        $distribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0];
        foreach ($logs as $log) {
            $distribution[$log->mood_score] = ($distribution[$log->mood_score] ?? 0) + 1;
        }

        return response()->json([
            'green_space_id' => $greenSpaceId,
            'days' => $days,
            'total_entries' => $logs->count(),
            'daily' => $daily,
            'distribution' => $distribution,
        ]);
    }
}
