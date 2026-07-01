<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceRestTest extends TestCase
{
    use RefreshDatabase;
    /**
     * 休憩入・戻のテスト
     */
    public function test_休憩入りボタンが正しく機能する(): void
    {
        $user = User::factory()->create();

        //出勤状態を作る
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->format('Y-m-d'),
            'status' =>Attendance::STATUS_WORKING,
            'start_time' =>now()->subHour(),
        ]);

        $this->actingAs($user);

        //休憩入ボタンがあるか確認
        $this->get('/attendance')->assertSee('休憩入');

        //休憩入ボタンを押す
        $this->post('/attendance/rest/start');

        //ステータスが休憩中になっているか確認
        $this->get('/attendance')->assertSee('休憩中');
        $this->assertEquals(Attendance::STATUS_BREAK,$attendance->fresh()->status);
    }

    public function test_休憩戻ボタンが正しく機能する(): void
    {
        $user = User::factory()->create();

        //休憩状態を作る
        Attendance::create([
            'user_id' => $user->id,
            'date' => now()->format('Y-m-d'),
            'status' => Attendance::STATUS_BREAK,
            'start_time' => now()->subHour(2),
        ]);

        $this->actingAs($user);

        //休憩戻処理
        $this->post('/attendance/rest/end');

        //ステータスが出勤中に戻る
        $this->get('/attendance')->assertSee('出勤中');
    }

    public function test_休憩は一日に何度でもできる():void
    {
        $user = User::factory()->create();

        //出勤状態を作る
        Attendance::create([
            'user_id' => $user->id,
            'date' => now()->format('Y-m-d'),
            'status' => Attendance::STATUS_WORKING,
            'start_time' => now()->subHour(5),
        ]);

        $this->actingAs($user);

        //休憩入->休憩戻->休憩入を繰り返す
        $this->post('/attendance/rest/start');
        $this->post('/attendance/rest/end');
        $this->post('/attendance/rest/start');

        //ステータスが休憩中になっているか確認
        $this->get('/attendance')->assertSee('休憩中');

        //休憩戻ボタン（休憩中）が表示されている
        $this->get('/attendance')->assertSee('休憩戻');

        //休憩履歴が2件存在するかを確認
        $this->assertDatabaseCount('rests',2);

    }

}
