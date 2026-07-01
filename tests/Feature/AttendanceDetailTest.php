<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    /**
     *　勤怠詳細機能の検証
     */
    public function test_勤怠詳細画面の内容が正しい(): void
    {
        $user = User::factory()->create(['name' => 'テスト太郎']);

        //勤怠データの作成
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-07-01',
            'status' => Attendance::STATUS_FINISHED,
            'start_time' => '10:00:00',
            'end_time' => '19:00:00',
        ]);

        //休憩データの作成
        $rest = Rest::create([
            'attendance_id' => $attendance->id,
            'start' => '12:00:00',
            'end' => '13:00:00',
        ]);

        $this->actingAs($user);

        //詳細ページに遷移
        $response = $this->get('/attendance/detail/'.$attendance->id);



        //各項目が一致しているか確認
        $response->assertSee('テスト太郎');
        $response->assertSee('2026年 7月1日');
        $response->assertSee('10:00');
        $response->assertSee('19:00');
        $response->assertSee('12:00');
        $response->assertSee('13:00');
    }
}
