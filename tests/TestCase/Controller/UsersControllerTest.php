<?php

namespace Gotea\Test\TestCase\Controller;

/**
 * UsersControllerのテスト
 */
class UsersControllerTest extends AppTestCase
{
    /**
     * 初期表示
     *
     * @return void
     */
    public function testIndex()
    {
        $this->get('/');
        $this->assertResponseOk();
        $this->assertResponseContains('<h1 class="page-title">Gotea</h1>');
    }

    /**
     * 未ログインからのリダイレクト
     *
     * @return void
     */
    public function testNotLogged()
    {
        $this->get('/players');
        $this->assertRedirect(['_name' => 'top', 'redirect' => '/players']);
    }

    /**
     * ログイン（失敗）
     *
     * @return void
     */
    public function testLoginFailedNoInput()
    {
        $this->enableCsrfToken();
        $this->post('/login', [
            'account' => '',
            'password' => '',
        ]);
        // $this->assertResponseError();
        $this->assertResponseContains('<h1 class="page-title">Gotea</h1>');
    }

    /**
     * ログイン（失敗）
     *
     * @return void
     */
    public function testLoginFailedLength()
    {
        if (!env('API_URL')) {
            $this->markTestSkipped('Environment variable `API_URL` is not set.');
        }
        $this->enableCsrfToken();
        $this->post('/login', [
            'account' => 'aaaa',
            'password' => 'bbbb',
        ]);
        $this->assertResponseError();
        $this->assertResponseContains('<h1 class="page-title">Gotea</h1>');
    }

    /**
     * ログイン（失敗）
     *
     * @return void
     */
    public function testLoginFailedInvalidAccount()
    {
        if (!env('API_URL')) {
            $this->markTestSkipped('Environment variable `API_URL` is not set.');
        }
        $this->enableCsrfToken();
        $this->post('/login', [
            'account' => 'aaaaaaaa',
            'password' => env('TEST_PASSWORD'),
        ]);
        $this->assertResponseError();
        $this->assertResponseContains('<h1 class="page-title">Gotea</h1>');
    }

    /**
     * ログイン（失敗）
     *
     * @return void
     */
    public function testLoginFailedInvalidPassword()
    {
        if (!env('API_URL')) {
            $this->markTestSkipped('Environment variable `API_URL` is not set.');
        }
        $this->enableCsrfToken();
        $this->post('/login', [
            'account' => env('TEST_USER'),
            'password' => 'bbbbbbbb',
        ]);
        $this->assertResponseError();
        $this->assertResponseContains('<h1 class="page-title">Gotea</h1>');
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
            'account' => env('TEST_USER'),
            'password' => env('TEST_PASSWORD'),
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
        $this->_createSession();
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
        $this->_createSession();
        $this->get('/logout');
        $this->assertResponseCode(302);
    }
}
