<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use App\Models\AttendanceCorrection;
use App\Models\RestCorrection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCorrectionTest extends TestCase
{
    use RefreshDatabase;
    private User $admin;
    private User $staff;
    private Attendance $attendance;
    private Rest $rest;
    private AttendanceCorrection $correction;
    private RestCorrection $restCorrection;

    /**
     * 勤怠情報修正機能の検証
     */
    protected function setUp():void{
        parent::setUp();

        //管理者と一般ユーザーの作成
        $this->admin = User::factory()->create(['role' => '1']);
        $this->staff = User::factory()->create();

        //元の勤怠と休憩データを作成
        $this->attendance = Attendance::factory()->create([
            'user_id' => $this->staff->id,
            'date' => '2026-07-03',
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        $this->rest = Rest::create([
            'attendance_id' => $this->attendance->id,
            'start' =>'12:00:00',
            'end' =>'13:00:00',
        ]);

        //修正申請データを作成
        $this->correction = AttendanceCorrection::create([
            'attendance_id' => $this->attendance->id,
            'user_id' => $this->staff->id,
            'date' => '2026-07-03',
            'start' => '10:00:00',
            'end' => '19:00:00',
            'status' => 0,
            'reason' => 'テスト修正のため',
        ]);

        $this->restCorrection = RestCorrection::create([
            'attendance_correction_id' => $this->correction->id,
            'rest_id' => $this->rest->id,
            'start' => '13:00:00',
            'end' => '14:00:00',
            'status' => 0,
        ]);
    }

    public function test_修正申請の詳細内容が正しく表示される(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin/stamp_correction_request/detail/'.$this->correction->id);

        $response->assertStatus(200);

        //AttendanceCorrectionの申請内容が表示されているか
        $response->assertSee('10:00');
        $response->assertSee('19:00');

        //RestCorrectionの申請内容が表示されているか
        $response->assertSee('13:00');
        $response->assertSee('14:00');
    }

    public function test_修正申請の承認処理が正しく行われテーブルが上書きされる():void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.correction_request_approve',['id' => $this->correction->id]));

        $response->assertRedirect(route('admin.correction_request_detail', ['id' => $this->correction->id]));

        //Attendance テービリが上書きされたか
        $this->assertDatabaseHas('attendances',[
            'id' => $this->attendance->id,
            'start_time' => '10:00:00',
            'end_time' => '19:00:00',
        ]);

        //Rest テーブルが上書きされたか
        $this->assertDatabaseHas('rests',[
            'id' => $this->rest->id,
            'start' => '13:00:00',
            'end' => '14:00:00',
        ]);

        //AttendanceCorrectionのステータスが１になったか
        $this->assertDatabaseHas('attendance_corrections',[
            'id' => $this->correction->id,
            'status' => 1,
        ]);

        //RestCorrection のステータスが1になったか
        $this->assertDatabaseHas('rest_corrections',[
                'id' => $this->restCorrection->id,
                'status' => 1,
            
        ]);

    }
}
