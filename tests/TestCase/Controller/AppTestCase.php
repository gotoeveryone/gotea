<?php
namespace Gotea\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;

/**
 * テスト時の共通コントローラ
 */
abstract class AppTestCase extends IntegrationTestCase
{
    /**
     * セッションデータ生成
     *
     * @return void
     */
    protected function _createSession()
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
