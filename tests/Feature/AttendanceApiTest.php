<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Testcase;
use App\Models\user;
use App\Models\Attendance;

/**
 * 勤怠APIの統合テストクラス
 * *ユーザーの勤怠データに関するAPIエンドポイントの動作を検証する。
 */

class AttendanceApiTest extends TestCase
{

    use RefreshDatabase;

    /**
     * 各テストメソッド実行前の事前じゅん
     * * データベースをリセットしシーダーから初期データを投入する
     * 
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    /**
     * 勤怠一覧テスト
     * * 認証済みユーザーがGETリクエストを送った際、
     * ページネーション情報を含んだJSONデータが200　OK
     * で返ることを検証する。
     * 
     * @return void
     */
    public function test_GET_勤怠一覧がJSONで取得できる(): void
    {
        /** @ver User $user シーダーで作成されたユーザーを取得 */
        $user =User::first();

        $response = $this->actingAs($user,'sanctum')->getJson('/api/v1/attendance-records');

        $response->assertStatus(200)->assertJsonStructure([
            'data'=>[
                '*' =>['id','user_id','date','start_time','end_time']
            ],
            'meta' => ['current_page','last_page','per_page','total']
            
        ]);
    }

    /**
     * 勤怠作成テスト
     * 正常なデータでPOSTリクエストを送った際、
     * 201 createdが返り、データベースに保存される事を検証する。
     * 
     * @return void
     */

    public function test_POST_勤怠が作成される(): void
    {
        /**@ver User $user */
        $user = User::first();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/attendance-records',[
            'date' =>'2026-07-01',
            'start_time' =>'09:00:00',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('attendances',[
            'user_id' => $user->id,
            'date' => '2026-07-01',
            'start_time' => '09:00:00',
        
        ]);
    }

    /**
     * バリデーションエラーテスト
     ** 必須項目が欠落した状態でPOSTリクエストを送った際、422 Unprocessable Entityが返ることを検証する。
     * @return void
     */
    public function test_POST_バリデーションエラー時に422と日本語絵エラーメッセージが返る():void
    {
        /** @var User $user */
        $user = User::first();

        $response = $this->actingAs($user,'sanctum')->postJson('/api/v1/attendance-records',[]);

        $response->assertStatus(422)->assertJsonValidationErrors(['date','start_time']);
    }

    /**
     * 未承認ユーザーのアクセス拒否テスト
     ** トークンを持たないゲストユーザーがAPIを叩いた際、401 Unauthenticatedで弾かれることを検証する。
     * @return void
     */

    public function test_未承認時に書き込み系APIで401が返る(): void
    {
        $response = $this->postJson('/api/v1/attendance-records',[
            'date' => '2026-07-01',
            'start_time' =>'09:00:00',
        ]);

        $response->assertStatus(401)->assertJson(['message' => 'Unauthenticated.']);
    }

    /**
     * 他ユーザーのデータ操作拒否テスト
     ** Policyクラスの制限により、自分以外の勤怠データを更新しようとした際に 403 Forbiddenで弾かれることを検証する。
     *
     * @return void
     */
    public function test_他ユーザーの勤怠を更新しようとすると403が返る():void
    {
        $users = User::take(2)->get();
        $user1 = $users[0];
        $user2 = $users[1];

        $attendanceUser1 = Attendance::where('user_id', $user1->id)->first();


        $response = $this->actingAs($user2, 'sanctum')
            ->putJson("/api/v1/attendance-records/{$attendanceUser1->id}", [
            'end_time' => '18:00:00'
            ]);
        $response->assertStatus(403);
    }

}
