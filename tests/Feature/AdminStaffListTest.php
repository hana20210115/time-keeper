<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminStaffListTest extends TestCase
{
    use RefreshDatabase;
    private User $admin;
    private User $staff;

    /**
     * ユーザー情報取得機能（管理者）の検証
     */
    protected function setUp():void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => '1']);
        $this->staff = User::factory()->create(['name' => 'テスト太郎', 'email' => 'taro@example.com']);
    }

    public function test_管理者が全ユーザーの氏名とメールアドレスを確認できる(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin/staff/list');

        $response->assertStatus(200);
        $response->assertSee('テスト太郎');
        $response->assertSee('taro@example.com');
    }

    public function test_ユーザーの勤怠情報が正しく表示される():void
    {
        $this->actingAs($this->admin);

        Attendance::create([
            'user_id' => $this->staff->id,
            'date' => '2026-07-03',
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        $response = $this->get('/admin/staff/detail/'.$this->staff->id);

        $response->assertStatus(200);
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    public function test_前月ボタンで前月の勤怠が表示されるか():void
    {
        $this->actingAs($this->admin);
        $targetMonth = '2026-06';

        $response = $this->get('/admin/staff/detail/'.$this->staff->id. '?month='.$targetMonth);

        $response->assertStatus(200);
        $response->assertSee('2026/06');
    }

    public function test_翌月ボタンで翌月の勤怠が表示される():void
    {
        $this->actingAs($this->admin);
        $targetMonth = '2026-08';

        $response = $this->get('/admin/staff/detail/'.$this->staff->id.'?month='.$targetMonth);

        $response->assertStatus(200);
        $response->assertSee('2026/08');
    }

    public function test_詳細ボタンで勤怠詳細画面へ遷移する():void
    {
        $this->actingAs($this->admin);

        $attendance = Attendance::create([
            'user_id' => $this->staff->id,
            'date' => '2026-07-03',
            'start_time' => '09:00:00',
            'end_time' => '18:00:00'
        ]);

        $response = $this->get('/admin/staff/detail/'.$this->staff->id);

        $response->assertSee(route('admin.attendance_detail',['id' => $attendance->id]));
    }


}
