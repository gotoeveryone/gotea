<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Http\Client;
use Cake\Log\Log;

/**
 * JSON連携用コンポーネント
 *
 * @author      Kazuki Kamizuru
 * @since       2016/05/05
 */
class JsonComponent extends Component
{
    /**
     * APIをコールします。
     *
     * @param string $path
     * @param string $method
     * @param array $data
     * @param array $headers
     * @param bool $assoc
     * @return array|object
     */
    public function callApi(string $path, $method = 'get', $data = [], $headers = [], $assoc = true)
    {
        $callMethod = strtolower($method);
        $url = $this->__getApiPath($path);
        $data = (count($data) > 0 ? json_encode($data) : $data);
        $headers = $this->__createHeaders($headers);

        $http = new Client();
        $response = $http->$callMethod($url, $data, $headers);
        $body = $response->getBody();

        $this->response = $this->response->withStatus($response->getStatusCode());
        if ($response->isOk()) {
            return json_decode($body, $assoc);
        }

        // 失敗
        Log::error($body);
        return ['status' => $response->statusCode(), 'message' => "{$method}リクエストに失敗しました。"];
    }

    /**
     * APIサーバのURLを取得します。
     *
     * @param string パス
     * @return string 対象APIのURL
     */
    private function __getApiPath(string $path)
    {
        $url = env('API_URL', 'http://localhost/');
        $length = strlen($url);

        if (substr($url, -$length) === $length) {
            $url .= '/';
        }

        return $url.$path;
    }

    /**
     * 認可ヘッダを生成します。
     *
     * @param array $headers
     * @return string ヘッダ情報
     */
    private function __createHeaders($optionHeaders = [])
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
