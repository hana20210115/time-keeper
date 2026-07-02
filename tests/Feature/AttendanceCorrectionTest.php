<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use App\Models\AttendanceCorrection;
use App\Models\RestCorrection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceCorrectionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Attendance $attendance;
    private Rest $rest;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['name'=>'テスト太郎']);

        $this->attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => '2026-07-01',
            'status' => Attendance::STATUS_FINISHED,
            'start_time' => '10:00:00',
            'end_time' => '19:00:00',
        ]);

        $this->rest = Rest::create([
            'attendance_id' => $this->attendance->id,
            'start' => '12:00:00',
            'end' => '13:00:00',
        ]);
    }

    public function test_出勤時間が退勤時間より後の場合はバリデーションエラー(): void
    {
        $this->actingAs($this->user);

        $response = $this->post('/attendance/correction/'.$this->attendance->id, [
            'date' => '2026-07-01', 
            'start_time' => '20:00:00', 
            'end_time' => '19:00:00',
            'reason' => '修正の備考入力'
        ]);


        $response->assertSessionHasErrors(['start_time' => '出勤時間もしくは退勤時間が不適切な値です']);
    }

    public function test_休憩開始時間が退勤時間より後の場合はバリデーションエラー(): void
    {
        $this->actingAs($this->user);

        $response = $this->post('/attendance/correction/'.$this->attendance->id, [
            'date' => '2026-07-01', 
            'start' => '10:00:00',
            'end' => '19:00:00',
            'rests' => [
                1 => [
                    'start' => '20:00:00',
                    'end' => '20:30:00',
                ]
            ],
            'reason' => '修正の備考入力',
        ]);

        $response->assertSessionHasErrors(['rests.1.start' => '休憩時間が不適切な値です']);
    }

    public function test_休憩終了時間が退勤時間より後の場合はバリデーションエラー(): void
    {
        $this->actingAs($this->user);

        $response = $this->post('/attendance/correction/'.$this->attendance->id, [
            'date' => '2026-07-01', // 💡 日付を追加
            'start' => '10:00:00',
            'end' => '19:00:00',
            'rests' => [
                1 => [
                    'start' => '12:00:00',
                    'end' => '20:00:00',
                ]
            ],
            'reason' => '修正の備考入力',
        ]);

        $response->assertSessionHasErrors(['rests.1.end' => '休憩時間もしくは退勤時間が不適切な値です']);
    }

    public function test_備考欄が未入力の場合はバリデーションエラー(): void
    {
        $this->actingAs($this->user);

        $response = $this->post('/attendance/correction/'.$this->attendance->id, [
            'date' => '2026-07-01', // 💡 日付を追加
            'start' => '10:00:00',
            'end' => '19:00:00',
            'reason' => ''
        ]);

        
        $response->assertSessionHasErrors(['reason' => '備考を記入して下さい']);
    }

    public function test_修正申請が実行され管理者の画面に表示される(): void
    {
        $admin = User::factory()->create([
            'role' => '1' 
        ]);

        $this->actingAs($this->user);

        $response = $this->post('/attendance/correction/'.$this->attendance->id, [
            'date' => '2026-07-01', 
            'start_time' => '11:00',
            'end_time' => '18:00',
            'rests' => [
                1 => [
                    'start' => '12:00',
                    'end' => '13:00'
                ]
            ],
            'reason' => '打刻忘れ'
        ]);
        

        $this->assertDatabaseHas('attendance_corrections', [
            'attendance_id' => $this->attendance->id,
            'start' => '11:00:00',
            'status' => 0, 
            'reason' => '打刻忘れ',
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin/stamp_correction_request/list');
        $response->assertStatus(200);
        $response->assertSee('承認待ち');
        $response->assertSee('打刻忘れ');
    }

    public function test_一般ユーザーの申請一覧画面の承認待ちに自分の申請が表示される(): void
    {
        $this->actingAs($this->user);

        AttendanceCorrection::create([
            'attendance_id' => $this->attendance->id,
            'user_id' => $this->user->id,
            'date' => '2026-07-01', 
            'start' => '11:00:00',
            'end' => '18:00:00',
            'status' => 0,
            'reason' => '表示されているかテスト'
        ]);

        $response = $this->get('/stamp_correction_request/list?tab=pending');
        $response->assertStatus(200);
        $response->assertSee('表示されているかテスト');
    }

    public function test_一般ユーザーの申請一覧画面の承認済みに承認された申請が表示される(): void
    {
        $this->actingAs($this->user);

        AttendanceCorrection::create([
            'attendance_id' => $this->attendance->id,
            'user_id' => $this->user->id,
            'date' => '2026-07-01', 
            'start' => '11:00:00',
            'end' => '18:00:00',
            'status' => 1,
            'reason' => '承認された申請のテスト'
        ]);

        $response = $this->get('/stamp_correction_request/list?tab=approved');
        $response->assertStatus(200);
        $response->assertSee('承認された申請のテスト');
    }

    public function test_申請一覧から詳細を押下すると勤怠詳細画面に遷移する(): void
    {
        $this->actingAs($this->user);

        $correction = AttendanceCorrection::create([
            'attendance_id' => $this->attendance->id,
            'user_id' => $this->user->id,
            'date' => '2026-07-01', 
            'start' => '11:00:00',
            'end' => '18:00:00',
            'status' => 0,
            'reason' => '画面遷移テスト用'
        ]);

        $response = $this->get('/attendance/detail/'.$this->attendance->id);
        $response->assertStatus(200);
        $response->assertSee('勤怠詳細');
    }
}