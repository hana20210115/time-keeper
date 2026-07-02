<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;

/**
 * 勤怠APIの統合テストクラス
 */
class AttendanceApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_GET_勤怠一覧がJSONで取得できる(): void
    {
        $user = User::first();
        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/attendance-records');

        $response->assertStatus(200)->assertJsonStructure([
            'data' => [
                '*' => ['id', 'user_id', 'date', 'start_time', 'end_time']
            ],
            'meta' => ['current_page', 'last_page', 'per_page', 'total']
        ]);
    }

    public function test_POST_勤怠が作成される(): void
    {
        $user = User::first();
        // 💡 シーダーと被らない未来の日付を使用
        $futureDate = now()->addYear()->format('Y-m-d');

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/attendance-records', [
            'date' => $futureDate,
            'start_time' => '09:00:00',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'date' => $futureDate,
            'start_time' => '09:00:00',
        ]);
    }

    public function test_POST_バリデーションエラー時に422と日本語エラーメッセージが返る(): void
    {
        $user = User::first();
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/attendance-records', []);

        $response->assertStatus(422)->assertJsonValidationErrors(['date', 'start_time']);
    }

    public function test_未承認時に書き込み系APIで401が返る(): void
    {
        $response = $this->postJson('/api/v1/attendance-records', [
            'date' => now()->addYears(2)->format('Y-m-d'),
            'start_time' => '09:00:00',
        ]);

        $response->assertStatus(401)->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_他ユーザーの勤怠を更新しようとすると403が返る(): void
    {
        $users = User::take(2)->get();
        $attendanceUser1 = Attendance::where('user_id', $users[0]->id)->first();

        $response = $this->actingAs($users[1], 'sanctum')
            ->putJson("/api/v1/attendance-records/{$attendanceUser1->id}", [
                'end_time' => '18:00:00'
            ]);
        $response->assertStatus(403);
    }

    public function test_GET_勤怠詳細がJSONで取得できる(): void
    {
        $user = User::first();
        $attendance = Attendance::where('user_id', $user->id)->first();

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/v1/attendance-records/{$attendance->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'user_id', 'date', 'start_time', 'end_time', 'status']
            ]);
    }

    public function test_GET_存在しないIDでは404とエラーJSONが返る(): void
    {
        $user = User::first();
        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/attendance-records/99999');

        $response->assertStatus(404)->assertJson([
            'error' => '勤怠情報が見つかりませんでした。'
        ]);
    }

    public function test_PUT_勤怠が更新される(): void
    {
        $user = User::first();
        $attendance = Attendance::where('user_id', $user->id)->first();
        // 💡 既存データと被らない日付に更新
        $newDate = now()->addYears(3)->format('Y-m-d');

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/v1/attendance-records/{$attendance->id}", [
            'date' => $newDate,
            'start_time' => '08:00:00',
            'end_time' => '19:00:00',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'end_time' => '19:00:00',
        ]);
    }

    public function test_DELETE_勤怠が削除される(): void
    {
        $user = User::first();
        $attendance = Attendance::where('user_id', $user->id)->first();

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/v1/attendance-records/{$attendance->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('attendances', [
            'id' => $attendance->id,
        ]);
    }
}