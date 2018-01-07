<?php
namespace Gotea\Test\TestCase\Controller\Api;

use Cake\TestSuite\IntegrationTestCase;

/**
 * APIテスト時の共通コントローラ
 */
abstract class ApiTestCase extends IntegrationTestCase
{
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
    public function enableApiUser(string $username = 'test')
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
    public function getEmptyResponse()
    {
        return $this->getCompareJsonResponse($this->emptyResponse);
    }

    /**
     * 404レスポンスを取得します。
     *
     * @return string
     */
    public function getNotFoundResponse()
    {
        return $this->getCompareJsonResponse($this->notFoundResponse);
    }

    /**
     * 比較用のJSONレスポンスを取得します。
     *
     * @param array $data
     * @return string
     */
    public function getCompareJsonResponse(array $data) : string
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }
}
