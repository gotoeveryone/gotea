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
     * Asserts content does not exist in the response body.
     *
     * @param mixed $content The content to check for.
     * @param string $message The failure message that will be appended to the generated message.
     * @return void
     */
    public function assertResponseNotEquals($content, $message = '')
    {
        if (!$this->_response) {
            $this->fail('No response set, cannot assert content. ' . $message);
        }
        $this->assertNotEquals($content, $this->_getBodyAsString(), $message);
    }

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
     * 比較用のJSONレスポンスを取得します。
     *
     * @param array $data
     * @return string
     */
    protected function getCompareJsonResponse(array $data) : string
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }
}
