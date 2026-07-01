<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceFinishTest extends TestCase
{
    use RefreshDatabase;
    /**
     * 退勤機能テスト
     * 
     */
    public function test_退勤ボタンが正しく機能する(): void
    {
        $user = User::factory()->create();

        //出勤状態を作る
        Attendance::create([
            'user_id' => $user->id,
            'date' => now()->format('Y-m-d'),
            'status' => Attendance::STATUS_WORKING,
            'start_time' => now()->subHour(8),
        ]);

        $this->actingAs($user);

        //退勤ボタンがあるか確認
        $this->get('/attendance')->assertSee('退勤');

        //退勤ボタンを押す
        $this->post('/attendance/end');

        //ステータスが退勤済みになっているか確認
        $this->get('/attendance')->assertSee('退勤済み');

        //データベースに退勤時間が保温されているか確認
        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'status' => Attendance::STATUS_FINISHED,
        ]);
        
        //退勤時間がNULLではないことを確認
        $this->assertNotNull(Attendance::where('user_id', $user->id)->first()->end_time);
    
    }

    public function test_退勤時間が勤怠一覧画面で確認できる():void
    {
        $user = User::factory()->create();

        //出勤->退勤のデータを作る
        Attendance::create([
            'user_id' => $user->id,
            'date' => now()->format('Y-m-d'),
            'status' =>Attendance::STATUS_FINISHED,
            'start_time' => now()->subHours(9),
            'end_time' => now()->subHours(1),
        ]);

        $this->actingAs($user);

        //勤怠一覧画面に遷移
        $response = $this->get('/attendance/list');

        //勤怠一覧画面に退勤時間が表示されているか確認
        $response->assertSee(now()->subHours(1)->format('H:i'));
    }
}
