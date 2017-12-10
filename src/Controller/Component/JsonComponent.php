<?php

namespace Gotea\Controller\Component;

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
     * @param string $path パス
     * @param string $method メソッド
     * @param array $data データ
     * @param array $headers ヘッダ
     * @param bool $assoc オブジェクト出力するか
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

        // \Cake\Http\Client\Messsage には`200`～`202`の定義しかないため、
        // \Cake\Http\Client\Responseのこのメソッドは`204`をOKとしていない
        if ($response->isOk() || $response->getStatusCode() === 204) {
            return [
                'status' => $response->getStatusCode(),
                'content' => json_decode($body, $assoc),
            ];
        }

        Log::error("StatusCode: {$response->getStatusCode()}");
        Log::error($body);

        return [
            'status' => $response->getStatusCode(),
            'message' => "{$method}リクエストに失敗しました。",
        ];
    }

    /**
     * APIサーバのURLを取得します。
     *
     * @param string $path パス
     * @return string 対象APIのURL
     */
    private function __getApiPath(string $path)
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
