<?php

namespace Database\Seeders;

use App\Models\MoodLogModel;
use Illuminate\Database\Seeder;

// Sedikit riwayat 7 hari terakhir supaya GET /api/mood-logs/summary tidak
// kosong sebelum ada pengguna sungguhan mengisi Mood Tracker.
class MoodLogSeeder extends Seeder
{
    public function run(): void
    {
        MoodLogModel::query()->delete();

        $scoresPerDay = [2, 1, 3, 2, 4, 3, 4]; // representatif utk 7 hari terakhir, terlama di indeks 0

        foreach ($scoresPerDay as $daysAgo => $baseScore) {
            $offset = 6 - $daysAgo;
            $entries = rand(2, 4);
            for ($i = 0; $i < $entries; $i++) {
                $activities = ['belajar', 'bekerja', 'olahraga', 'lainnya'];
                MoodLogModel::create([
                    'green_space_id' => 1,
                    'mood_score' => max(1, min(4, $baseScore + rand(-1, 1))),
                    'activity' => $activities[array_rand($activities)],
                    'anonymous_session_id' => 'seed-session-' . $offset . '-' . $i,
                    'logged_at' => now()->subDays($offset)->setTime(rand(7, 19), rand(0, 59)),
                ]);
            }
        }
    }
}
