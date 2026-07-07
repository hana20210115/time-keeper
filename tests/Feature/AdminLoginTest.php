<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;
    /**
     * 管理者ログインのバリデーションエラーチェック():void
     */
    public function test_管理者ログインのバリデーションエラーチェック(): void
    {
        $user = User::factory()->create([
            'name' => '管理者',
            'email' => 'admin-test@example.com',
            'password' => bcrypt('password'),
            'role' => 1,
        ]);

        //メールアドレス未入力
        $this->post('/login',[
            'email' => '',
            'password' => 'password',
        ])->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
        

        

        //パスワード未入力
        $this->post('/login',[
            'email' => 'admin-test@example.com',
            'password' => '',
        ])->assertSessionHasErrors(['password'=>'パスワードを入力してください']);

        //登録内容と一致しない
        $this->post('/login',[
            'email' => 'admin-test@example.com',
            'password'=>'12345678',
        ])->assertSessionHasErrors(['email'=>'ログイン情報が登録されていません']);
    }

    /**
     * 管理者の正常なログイン確認
     * 
     */

    public function test_正常な管理者ログインで管理画面に遷移する():void
    {
        $admin =User::factory()->create([
            'name' => '管理者',
            'email' => 'admin-success@example.com',
            'password'=>bcrypt('password123'),
            'role'=>1,
        ]);

        $response = $this->post('/login',[
            'email' => 'admin-success@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin/attendance/list');
        $this->assertAuthenticatedAs($admin);
    }
}
