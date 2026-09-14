<?php

namespace Tests\Feature;

use App\Models\MoodLogModel;
use App\Models\UserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MoodLogApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_tamu_tanpa_login_bisa_mengirim_mood(): void
    {
        $response = $this->postJson('/api/mood-logs', [
            'green_space_id' => 1,
            'mood_score' => 3,
            'activity' => 'olahraga',
            'anonymous_session_id' => 'guest-session',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('mood_logs', [
            'green_space_id' => 1,
            'mood_score' => 3,
            'user_id' => null,
        ]);
    }

    public function test_pengguna_yang_login_ikut_tercatat_user_id_nya(): void
    {
        $user = UserModel::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/mood-logs', ['green_space_id' => 1, 'mood_score' => 4])
            ->assertStatus(201);

        $this->assertDatabaseHas('mood_logs', ['green_space_id' => 1, 'user_id' => $user->id]);
    }

    public function test_mood_score_di_luar_1_sampai_4_ditolak(): void
    {
        $this->postJson('/api/mood-logs', ['green_space_id' => 1, 'mood_score' => 9])
            ->assertStatus(422);
    }

    public function test_summary_menghitung_distribusi_dan_tren_harian(): void
    {
        MoodLogModel::factory()->count(3)->create(['green_space_id' => 1, 'mood_score' => 4, 'logged_at' => now()]);
        MoodLogModel::factory()->count(1)->create(['green_space_id' => 1, 'mood_score' => 1, 'logged_at' => now()]);
        // Di luar jendela 7 hari — tidak boleh ikut terhitung.
        MoodLogModel::factory()->create(['green_space_id' => 1, 'mood_score' => 2, 'logged_at' => now()->subDays(30)]);

        $response = $this->getJson('/api/mood-logs/summary?green_space_id=1&days=7');

        $response->assertStatus(200)
            ->assertJsonPath('total_entries', 4)
            ->assertJsonPath('distribution.4', 3)
            ->assertJsonPath('distribution.1', 1);

        $daily = $response->json('daily');
        $this->assertCount(7, $daily);
    }
}
