<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 勤怠一覧情報取得機能の検証
     */
    public function test_勤怠一覧画面に自分の情報が表示され現在の月が表示される(): void
    {
        $user = User::factory()->create();

        //勤怠データを作成
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->format('Y-m-d'),
            'status' =>Attendance::STATUS_FINISHED,
            'start_time' => now()->subHours(9),
            'end_time' => now()->subHours(1),
        ]);

        $this->actingAs($user);

        //勤怠一覧ページを開く
        $response = $this->get('/attendance/list');

        //自分のデータが表示されているか確認
        $response->assertSee(now()->format('m/d'));

        //現在の月が行事されているか確認
        $response->assertSee(now()->format('Y/m'));
    }

    public function test_前月と翌月の切り替えができる():void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        //前月のリンクの確認(2026-06に移動できるか)
        $prevMonth = now()->subMonth()->format('Y/m');
        $response = $this->get('/attendance/list?month='.$prevMonth);
        $response->assertSee($prevMonth);

        //翌月のリンクの確認(2026-08に移動できるか)
        $nextMonth = now()->addMonth()->format('Y/m');
        $response = $this->get('/attendance/list?month='.$nextMonth);
        $response->assertSee($nextMonth);

    }

    public function test_詳細ボタンを押すと詳細画面に遷移する():void
    {
        $user = User::factory()->create();

        //勤怠データを作成
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->format('Y-m-d'),
            'status'=>Attendance::STATUS_FINISHED,
            'start_time' => now()->subHours(9),
            'end_time' => now()->subHours(1),
        ]);
        
        $this->actingAs($user);

        //詳細画面へ遷移する
        $response = $this->get('/attendance/detail/'.$attendance->id);
        
        //詳細画面に遷移できているか確認（ステータスコード200）
        $response->assertStatus(200);

    }
} 
