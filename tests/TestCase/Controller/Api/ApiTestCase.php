<?php
declare(strict_types=1);

namespace Gotea\Test\TestCase\Controller\Api;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * APIテスト時の共通コントローラ
 */
abstract class ApiTestCase extends TestCase
{
    use IntegrationTestTrait;

    /**
     * データなしレスポンス
     *
     * @var array
     */
    private $emptyResponse = [
        'response' => [],
    ];

    /**
     * 404レスポンス
     *
     * @var array
     */
    private $notFoundResponse = [
        'response' => [
            'code' => 404,
            'message' => 'Not Found',
        ],
    ];

    /**
     * APIユーザのヘッダを設定します。
     *
     * @param string $username ユーザ名
     * @return \Gotea\Test\TestCase\Controller\Api\ApiTestCase
     */
    protected function enableApiUser(string $username = 'test')
    {
        $this->configRequest([
            'headers' => [
                'X-Access-User' => $username,
            ],
        ]);

        return $this;
    }

    /**
     * 空レスポンスを取得します。
     *
     * @return string
     */
    protected function getEmptyResponse()
    {
        return $this->getCompareJsonResponse($this->emptyResponse);
    }

    /**
     * 404レスポンスを取得します。
     *
     * @return string
     */
    protected function getNotFoundResponse()
    {
        return $this->getCompareJsonResponse($this->notFoundResponse);
    }

    /**
     * レスポンスを配列に変換して返却します。
     *
     * @return array
     */
    protected function getResponseArray(): array
    {
        return json_decode($this->_getBodyAsString(), true);
    }

    /**
     * 比較用のJSONレスポンスを取得します。
     *
     * @param array $data
     * @return string
     */
    protected function getCompareJsonResponse(array $data): string
    {
        return json_encode($data);
    }
}
