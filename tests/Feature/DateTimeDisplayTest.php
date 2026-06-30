<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class DateTimeDisplayTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 勤怠打刻画面に現在の日時が正しい形式で表示されているか検証する
     */
    public function test_勤怠打刻画面に現在の日時が正しい形式で表示されている(): void
    {
        $knownDate = Carbon::create(2026, 6,30,12,34,56);
        Carbon::setTestNow($knownDate);

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/attendance');

        $response->assertStatus(200);

        $expectedDate = $knownDate->format('Y年m月d日');
        $expectedTime = $knownDate->format('H:i');

        $response->assertSee($expectedDate);
        $response->assertSee($expectedTime);
    }
}
