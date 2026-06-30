<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AttendanceReportTest extends TestCase
{
    /**
     * ゲストは勤怠レポートにアクセスできない
     */
    public function test_未承認でアクセスするとログイン画面にリダイレクトされる(): void
    {
        $response = $this->get('/attendance/report');
        $response->assertRedirect('/login');
    }

    /**
     * 統計情報が正しく計算されるか検証
     */

    public function test_認証ユーザーの統計情報が正しく計算される():void
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => now()->format('Y-m-d'),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        $attendance->rests()->create([
            'start' => '12:00:00',
            'end' => '13:00:00',
        ]);
        
        $response = $this->actingAs($user)->get('/attendance/report');

        $response->assertStatus(200);

        $response->assertViewHas('viewData',function ($viewData){
           return $viewData['total_work_time'] === '8h 0m' ;
        });
    }

    public function test_遅刻回数が正しくカウントされる():void
    {
        $user = User::factory()->create();
        $this->actingAs($user,'web');

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => now()->format('Y-m-d'),
            'start_time' => '09:25:00',
            'end_time' => '18:00:00',
        ]);

        $response = $this->get('/attendance/report');

        $response->assertViewHas('viewData',function ($viewData){
            return $viewData['late_count'] === 1;
        });
    }
}
