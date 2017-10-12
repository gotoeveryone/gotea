<?php

namespace Gotea\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;

/**
 * UsersControllerのテスト
 */
class UsersControllerTest extends IntegrationTestCase
{
    /**
     * 画面が見えるか
     *
     * @return void
     */
    public function testDisplay()
    {
        $this->get('/');
        $this->assertResponseOk();
        $this->assertResponseContains('ログイン');
        $this->assertResponseContains('<html>');
    }

    /**
     * ログイン（失敗）
     *
     * @return void
     */
    public function testLoginFailed()
    {
        $this->get('/players');
        $this->assertRedirect(['controller' => 'Users', 'action' => 'index',
            'redirect' => '/players']);

        $invalidData = [
            // 未入力
            [
                'username' => '',
                'password' => '',
            ],
            // 桁数エラー
            [
                'username' => 'aaaa',
                'password' => 'bbbb',
            ],
            // パスワード不一致
            [
                'username' => env('TEST_USER'),
                'password' => 'bbbb',
            ],
            // ID不一致
            [
                'username' => 'aaaa',
                'password' => env('TEST_PASSWORD'),
            ],
        ];

        $this->enableCsrfToken();
        foreach ($invalidData as $data) {
            $this->post('/users/login', $data);
            $this->assertResponseError();
        }
    }

    /**
     * ログイン成功
     *
     * @return void
     */
    public function testLoginSuccess()
    {
        $this->enableCsrfToken();
        $data = [
            'username' => env('TEST_USER'),
            'password' => env('TEST_PASSWORD'),
        ];

        $this->post('/users/login', $data);
        $this->assertRedirect(['controller' => 'Players', 'action' => 'index']);
    }

    /**
     * セッションありでトップへ
     *
     * @return void
     */
    public function testLoggedTop()
    {
        $this->__createSession();

        $this->get('/');
        $this->assertRedirect(['controller' => 'Players', 'action' => 'index']);
    }

    public function testLogout()
    {
        $this->__createSession();

        $this->get('/users/logout');
        $this->assertResponseCode(302);
    }

    /**
     * セッションデータ生成
     *
     * @return void
     */
    private function __createSession()
    {
        $this->session([
            'Auth' => [
                'User' => [
                    'id' => 1,
                    'userName' => 'テスト',
                    'role' => '管理者',
                ],
            ],
        ]);
    }
}
