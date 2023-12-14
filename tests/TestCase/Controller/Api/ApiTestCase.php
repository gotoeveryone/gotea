<?php
declare(strict_types=1);

namespace Gotea\Test\TestCase\Controller\Api;

use Gotea\Test\TestCase\Controller\AppTestCase;

/**
 * APIテスト時の共通コントローラ
 */
abstract class ApiTestCase extends AppTestCase
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
