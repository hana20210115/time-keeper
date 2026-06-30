<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * バリデーションエラーの確認
     */
    public function test_会員登録バリデーションエラーチェック(): void
    {   // 名前がみにゅう録
        $this->post('/register',[
            'name'=>'',
            'email'=>'test@example.com',
            'password'=>'password123',
            'password_confirmation' =>'password123',
        ])->assertSessionHasErrors(['name'=>'お名前を入力して下さい。']);

        // メールアドレスが未入力
        $this->post('/register',[
        'name'=>'テスト太郎',
        'email'=>'',
        'password'=>'password123',
        'password_confirmation' =>'password123',
        ])->assertSessionHasErrors(['email'=>'メールアドレスを入力して下さい。']);
        
        // パスワードが8文字未満
        $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ])->assertSessionHasErrors(['password' => 'パスワードは8文字以上で入力して下さい。']);

        // パスワード不一致
        $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors(['password' => 'パスワードと一致しません。']);
    
        // パスワードが未入力
        // 3. パスワードが8文字未満
        $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ])->assertSessionHasErrors(['password' => 'パスワードを入力して下さい。']);
    }


    public function test_正常な入力でユーザーが登録される():void
    {
        $response = $this->post('/register',[
            'name'=>'テスト太郎',
            'email'=>'test6@example.com',
            'password'=>'password123',
            'password_confirmation'=>'password123',
        ]);

        $response->assertRedirect('/attendance');

        $this->assertDatabaseHas('users',['email' => 'test6@example.com']);
    }
}
