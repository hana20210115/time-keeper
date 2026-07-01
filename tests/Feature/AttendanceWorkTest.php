<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceWorkTest extends TestCase
{
    use RefreshDatabase;
    /**
     * 出勤機能のテスト
     */
    public function test_出勤ボタンが正しく機能する(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        //出勤ボタンがあるか確認
        $response = $this->get('attendance');
        $response->assertSee('出勤');

        //出勤ボタンを押す
        $this->post('/attendance/start');
        $this->assertDatabaseHas('attendances',[
            'user_id' => $user->id,
            'date' => now()->format('Y-m-d'),
            'status' => Attendance::STATUS_WORKING,
        ]);
    }

    public function test_出勤は1日一回のみできる():void
    {
        $user =User::factory()->create();

        //退勤積みデータを作る
        Attendance::create([
            'user_id' => $user->id,
            'date' => now()->format('Y-m-d'),
            'status' => Attendance::STATUS_FINISHED,
            'start_time' => now()->subHours(),
            'end_time' => now(),
        ]);

        $this->actingAs($user);

        // 勤怠打刻画面にアクセス
        $response = $this->get('/attendance');

        //出勤ボタンがないことを確認
        $response->assertDontSee('<button type="submit">出勤</button>');
    }

    public function test_出勤時間が勤怠一覧画面で確認できる():void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        //出勤ボタンを押す
        $this->post('/attendance/start');

        // 勤怠一覧画面に遷移
        $response = $this->get('/attendance/list');


        //今日の日付が表示されているか確認
        $response->assertSee(now()->format('m/d'));
    }
}
