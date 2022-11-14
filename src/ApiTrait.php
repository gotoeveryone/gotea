<?php
declare(strict_types=1);

namespace Gotea;

use Cake\Http\Client;
use Cake\Log\Log;

/**
 * APIコールを行うためのトレイト
 */
trait ApiTrait
{
    /**
     * APIをコールします。
     *
     * @param string $path パス
     * @param string $method メソッド
     * @param array $data データ
     * @param array $headers ヘッダ
     * @param bool $assoc オブジェクト出力するか
     * @return object|array
     */
    public function callApi(
        string $path,
        string $method = 'get',
        array $data = [],
        array $headers = [],
        bool $assoc = true
    ): array|object {
        $callMethod = strtolower($method);
        $url = $this->getApiPath($path);
        $data = (count($data) > 0 ? json_encode($data) : $data);
        $headers = $this->createHeaders($headers);

        $http = new Client();
        if (method_exists($http, $callMethod)) {
            /** @var \Cake\Http\Client\Response $response */
            $response = $http->$callMethod($url, $data, $headers);
            $body = $response->getStringBody();

            // ステータスコードが200～204なら正常終了とみなす
            if ($response->isOk()) {
                return [
                    'status' => $response->getStatusCode(),
                    'content' => json_decode($body, $assoc),
                ];
            }
        }

        Log::error("StatusCode: {$response->getStatusCode()}");
        Log::error($body);

        return [
            'status' => $response->getStatusCode(),
            'message' => "${method}リクエストに失敗しました。",
        ];
    }

    /**
     * APIサーバのURLを取得します。
     *
     * @param string $path パス
     * @return string 対象APIのURL
     */
    private function getApiPath(string $path): string
    {
        $url = env('API_URL', 'http://localhost/');
        $length = strlen($url);

        if (substr($url, -$length) === $length) {
            $url .= '/';
        }

        return $url . $path;
    }

    /**
     * 認可ヘッダを生成します。
     *
     * @param array $optionHeaders ヘッダに追加設定する情報
     * @return array ヘッダ情報
     */
    private function createHeaders(array $optionHeaders = []): array
    {
        $headers = [
            'Content-Type' => 'application/json',
        ];

        foreach ($optionHeaders as $key => $value) {
            $headers[$key] = $value;
        }

        return [
            'headers' => $headers,
        ];
    }
}
