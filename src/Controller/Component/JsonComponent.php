<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Http\Client;

/**
 * JSON連携用コンポーネント
 * 
 * @author		Kazuki Kamizuru
 * @since		2016/05/05
 */
class JsonComponent extends Component
{
    public $controller = null;
    public $session = null;
    public $components = ['MyAuth'];

    public function initialize(array $config)
    {
        parent::initialize($config);

        /**
         * Get current controller
        */
        $this->controller = $this->_registry->getController();
    }

    /**
     * アクセストークンを取得します。
     * 
     * @param $account
     * @param $password
     * @return type
     */
    public function saveAccessToken($account, $password)
    {
        $token = $this->sendResource('auth/login', 'post', [
            "account" => $account,
            "password" => $password
        ]);
        if ($token && isset($token['access_token'])) {
            // セッションにトークンを書き込み
            $this->request->session()->write('access_token', $token['access_token']);
            return $token['access_token'];
        }
        return null;
    }

    /**
     * アクセストークンを破棄します。
     * 
     * @return type
     */
    public function removeAccessToken()
    {
        return $this->sendResource('auth/logout', 'delete', [
            "access_token" => $this->request->session()->read('access_token')
        ]);
    }

    /**
     * APIをコールします。
     * 
     * @param string $url
     * @param string $method
     * @param bool $data
     * @param bool $assoc
     * @param bool $useOld 旧APIサーバを利用するかどうか
     */
    public function sendResource(string $url, string $method, $data = [], $assoc = true, $useOld = false)
    {
        // トークンが読み込めた場合はデータに追加
        if (($token = $this->request->session()->read('access_token'))) {
            $data['access_token'] = $token;
        }
        $http = new Client();
        $callMethod = strtolower($method);
        $response = $http->$callMethod($this->__getApiUrl($useOld).$url, $data, $this->__getCaArray());
        // アプリログイン済みだが、APIが401なら再認証
        if ($response->getStatusCode() == 401 && $this->MyAuth->user()) {
            $userId = $this->MyAuth->user('userId');
            $password = $this->MyAuth->user('password');
            // トークン再生成
            $data['access_token'] = $this->saveAccessToken($userId, $password);
            $response = $http->$callMethod($this->__getApiUrl().$url, $data, $this->__getCaArray());
        }
        $this->response = $this->response->withStatus($response->getStatusCode());
        if ($response->isOk()) {
            return json_decode($response->body(), $assoc);
        } else {
            return ['status' => $response->statusCode(), 'message' => "{$method}リクエストに失敗しました。"];
        }
    }

    /**
     * APIサーバのURLを取得します。
     * 
     * @param bool $useOld 旧APIサーバを利用するかどうか
     * @return string APIサーバのURL
     */
    private function __getApiUrl($useOld = false)
    {
        $serverName = env('SERVER_NAME');
        return "https://{$serverName}/".(!$useOld ? 'web-api/v1/' : 'web-resource/');
    }

    /**
     * CA証明書の配列を生成して取得します。
     * 
     * @return type
     */
    private function __getCaArray()
    {
        return [
            'ssl_cafile' => env('SSL_CA_CRT')
        ];
    }
}
