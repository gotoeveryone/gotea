<?php
namespace Gotea\Test\TestCase\Controller;

use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestCase;

/**
 * テスト時の共通コントローラ
 */
abstract class AppTestCase extends IntegrationTestCase
{
    /**
     * レスポンスがエラーになったかを確認します。
     *
     * @return void
     */
    protected function assertContainsError()
    {
        $this->assertResponseError();
        if (Configure::read('debug')) {
            $this->assertResponseContains('Error');
        } else {
            $this->assertResponseContains('見つかりません');
        }
    }

    /**
     * セッションデータ生成
     *
     * @return void
     */
    protected function createSession()
    {
        $this->session([
            'Auth' => [
                'User' => [
                    'id' => 1,
                    'account' => env('TEST_USER'),
                    'name' => 'テスト',
                    'role' => '管理者',
                ],
            ],
        ]);
    }
}
