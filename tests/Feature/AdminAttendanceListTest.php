<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Carbon;

class AdminAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => '1']); 
    }

    public function test_管理者が勤怠一覧画面にアクセスすると当日の全ユーザー情報が表示される(): void
    {
        $this->actingAs($this->admin);

        $today = Carbon::today()->format('Y/m/d');


        $user1 = User::factory()->create(['name' => 'ユーザーA']);
        $user2 = User::factory()->create(['name' => 'ユーザーB']);

        Attendance::create(['user_id' => $user1->id, 'date' => $today, 'start_time' => '09:00:00', 'end_time' => '18:00:00']);
        Attendance::create(['user_id' => $user2->id, 'date' => $today, 'start_time' => '10:00:00', 'end_time' => '19:00:00']);

        $response = $this->get('/admin/attendance/list');
        
        $response->assertStatus(200);
        $response->assertSee($today); 
        $response->assertSee('ユーザーA');
        $response->assertSee('ユーザーB');
    }

    public function test_前日ボタンを押すと前日の勤怠情報が表示される(): void
    {
        $this->actingAs($this->admin);
        
        $yesterday= Carbon::yesterday()->format('Y/m/d');

        
        
        $user = User::factory()->create(['name' => 'ユーザーA']);
        Attendance::create([
            'user_id' => $user->id, 
            'date' => $yesterday, 
            'start_time' => '09:00:00'
        ]);

        $response = $this->get('/admin/attendance/list?date=' . $yesterday);
        
        $response->assertStatus(200);
        $response->assertSee($yesterday);
        $response->assertSee('ユーザーA');
    }

    public function test_翌日ボタンを押すと翌日の勤怠情報が表示される(): void
    {
        $this->actingAs($this->admin);
        
        $tomorrow = Carbon::tomorrow()->format('Y/m/d');
        
        
        
        $user = User::factory()->create(['name' => 'ユーザーA']);
        Attendance::create([
            'user_id' => $user->id, 
            'date' => $tomorrow, 
            'start_time' => '09:00:00'
        ]);

        $response = $this->get('/admin/attendance/list?date=' . $tomorrow);
        
        $response->assertStatus(200);
        $response->assertSee($tomorrow);
        $response->assertSee('ユーザーA');
    }
}