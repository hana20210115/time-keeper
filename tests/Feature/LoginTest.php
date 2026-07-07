<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    /**
     * バリデーションエラーの確認
     */
    public function test_ログインバリデーションエラーチェック(): void
    {    
        $user = User::factory()->create([
            'email' =>'test7@example.com',
            'password' =>bcrypt('password123'),
        ]);
        //メールアドレス未入力
        $this->post('/login',[
            'email'=>'',
            'password'=>'password123',
        ])->assertSessionHasErrors(['email'=>'メールアドレスを入力してください']);

        //パスワード未入力
        $this->post('/login',[
            'email'=>'test7@example.com',
            'password'=>'',
        ])->assertSessionHasErrors(['password'=>'パスワードを入力してください']);

        // 登録内容と一致しない
        $this->post('/login',[
            'email' => 'test7@example.com',
            'password' => '12345678',
        ])->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません'
        ]);
    }

    /**
     * 正常なログインの確認
     */

    public function test_正常なログインで勤怠画面に遷移する():void
    {
        $user = User::factory()->create([
            'email'=>'test8@example.com',
            'password'=>bcrypt('password123'),
        ]);

        $response = $this->post('/login',[
            'email'=>'test8@example.com',
            'password'=>'password123'
        ]);

        $response->assertRedirect('/attendance');
        $this->assertAuthenticatedAs($user);
    }


}
