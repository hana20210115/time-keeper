<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAttendanceTest extends TestCase
{
    use RefreshDatabase;
    private User $admin;
    private Attendance $attendance;
    /**
     * 勤怠詳細情報取得・修正機能（管理者）の検証
     */

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => '1']);

        $user = User::factory()->create();
        $this->attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-07-03',
            'start_time' => '2026-07-03 09:00:00',
            'end_time' => '2026-07-03 18:00:00',
        ]);
        Rest::create([
            'attendance_id' => $this->attendance->id,
            'start' => '2026-07-03 12:00:00',
            'end' => '2026-07-03 13:00:00',
        ]);
    }

    public function test_管理者が出勤時間が退勤時間よりあとの場合エラー(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.attendance_update', ['id' => $this->attendance->id]), [
            'start_time' => '20:00',
            'end_time' => '19:00',
            'reason' => 'テスト修正'
        ]);

        $response->assertSessionHasErrors(['start_time' => '出勤時間もしくは退勤時間が不適切な値です']);
    }

    public function test_管理者が休憩開始時間が退勤時間より後の場合エラー(): void
    {
        $this->actingAs($this->admin);

        $restId = $this->attendance->rests->first()->id;

        $response = $this->post(route('admin.attendance_update', ['id' => $this->attendance->id]), [
            'start_time' => '09:00',
            'end_time' => '18:00',
            'rests' => [
                $restId => ['start' => '19:00', 'end' => '20:00']
            ],
            'reason' => 'テスト修正'
        ]);

        $response->assertSessionHasErrors(["rests.{$restId}.start" => '休憩時間が不適切な値です']);
    }

    public function test_管理者が備考欄未入力でエラーが出るか検証(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.attendance_update', ['id' => $this->attendance->id]), [
            'start_time' => '09:00',
            'end_time' => '18:00',
            'reason' => ''
        ]);

        $response->assertSessionHasErrors(['reason' => '備考を記入して下さい']);
    }

    public function test_管理者画面に選択した勤怠情報が表示されるか検証(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.attendance_detail', ['id' => $this->attendance->id]));

        $response->assertStatus(200);
        $response->assertSee('7月3日');
    }
}