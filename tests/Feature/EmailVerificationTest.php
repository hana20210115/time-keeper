<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 会員登録後、認証メールが送信される
     */
    public function test_会員登録をすると認証メールが送信される(): void
    {
        Notification::fake();

        $response = $this->withoutExceptionHandling()->post('/register',[
            'name' =>'テストユーザー',
            'email' => 'test5@example.com',
            'password' => 'password123',
            'password_confirmation'=>'password123',
        ]);

        

        $this->assertAuthenticated();

        $user = User::where('email','test5@example.com')->first();
        $this->assertNotNull($user);

        Notification::assertSentTo(
            [$user],
            VerifyEmail::class
        );
    }

    /**
     * メール認証を完了すると、勤怠登録画面に遷移する
     */
    Public function test_メール認証URLにアクセスすると認証が完了し勤怠画面に遷移する():void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id'=>$user->id,'hash'=>sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect('/attendance?verified=1');

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }
}
