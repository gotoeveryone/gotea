<?php
declare(strict_types=1);

namespace Gotea\Test\TestCase\Controller;

/**
 * UsersControllerのテスト
 */
class UsersControllerTest extends AppTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Users',
    ];

    /**
     * 初期表示
     *
     * @return void
     */
    public function testIndex()
    {
        $this->get('/');
        $this->assertResponseOk();
        $this->assertResponseContains('<h1 class="login-title">Gotea</h1>');
    }

    /**
     * 未ログインからのリダイレクト
     *
     * @return void
     */
    public function testNotLogged()
    {
        $this->get('/players');
        $this->assertRedirectEquals(['_name' => 'top', '?' => ['redirect' => '/players']]);
    }

    /**
     * ログイン（失敗）
     *
     * @return void
     */
    public function testLoginNoInput()
    {
        $this->enableCsrfToken();
        $this->post('/login', [
            'account' => '',
            'password' => '',
        ]);
        // $this->assertResponseError();
        $this->assertResponseContains('<h1 class="login-title">Gotea</h1>');
    }

    /**
     * ログイン（失敗・バリデーションエラー）
     *
     * @return void
     */
    public function testLogin400()
    {
        if (!env('API_URL')) {
            $this->markTestSkipped('Environment variable `API_URL` is not set.');
        }
        $this->enableCsrfToken();
        $this->post('/login', [
            'account' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
            'password' => 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb',
        ]);
        $this->assertResponseCode(400);
        $this->assertResponseContains('<h1 class="login-title">Gotea</h1>');
    }

    /**
     * ログイン（失敗・バリデーションエラー）
     *
     * @return void
     */
    public function testLogin401()
    {
        if (!env('API_URL')) {
            $this->markTestSkipped('Environment variable `API_URL` is not set.');
        }
        $this->enableCsrfToken();
        $this->post('/login', [
            'account' => 'baduser',
            'password' => 'badpassword',
        ]);
        $this->assertResponseCode(401);
        $this->assertResponseContains('<h1 class="login-title">Gotea</h1>');
    }

    /**
     * ログイン成功
     *
     * @return void
     */
    public function testLoginSuccess()
    {
        if (!env('API_URL')) {
            $this->markTestSkipped('Environment variable `API_URL` is not set.');
        }
        $this->enableCsrfToken();
        $this->post('/login', [
            'account' => 'testuser',
            'password' => 'password',
        ]);
        $this->assertRedirect(['_name' => 'players']);
    }

    /**
     * セッションありでトップへ（ログイン後の画面へリダイレクト）
     *
     * @return void
     */
    public function testLoggedTop()
    {
        $this->createSession();
        $this->get('/');
        $this->assertRedirect(['_name' => 'players']);
    }

    /**
     * ログアウト
     *
     * @return void
     */
    public function testLogout()
    {
        $this->createSession();
        $this->get('/logout');
        $this->assertResponseCode(302);
    }
}
