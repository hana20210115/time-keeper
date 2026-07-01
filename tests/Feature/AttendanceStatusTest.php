<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceStatusTest extends TestCase
{   

    use RefreshDatabase;
    /**
     * 勤怠ステータス表示のテスト
     * データ（Attendanceモデル）の状態によってステータス名が変わることを検証する
     */
    public function test_勤怠ステータスの表示確認(): void
    {   // 勤務外 status=0
        $user1 = User::factory()->create();
        $this->actingAs($user1);
        $this->get('/attendance')->assertSee('勤務外');

        //出勤中 status=1
        $user2 = User::factory()->create();
        Attendance::create([
            'user_id' => $user2->id,
            'date' => now()->format('Y-m-d'),
            'status' =>Attendance::STATUS_WORKING,
            'start_time' => now(),
        ]);

        $this->actingAs($user2);
        $this->get('/attendance')->assertSee('出勤中');

        //休憩中 status=2
        $user3 = User::factory()->create();
        Attendance::create([
            'user_id' =>$user3->id,
            'date' => now()->format('Y-m-d'),
            'status' => Attendance::STATUS_BREAK,
            'start_time' => now(),
        ]);
        $this->actingAs($user3);
        $this->get('/attendance')->assertSee('休憩中');

        //退勤済み status=3
        $user4 = User::factory()->create();
        Attendance::create([
            'user_id' =>$user4->id,
            'date' => now()->format('Y-m-d'),
            'status' => Attendance::STATUS_FINISHED,
            'start_time' => now(),
            'end_time' => now(),
        ]);
        $this->actingAs($user4);
        $this->get('/attendance')->assertSee('退勤済み');


    }
}
