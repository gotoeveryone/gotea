<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Http\Client;

/**
 * JSON連携用コンポーネント
 *
 * @author      Kazuki Kamizuru
 * @since       2016/05/05
 */
class JsonComponent extends Component
{
    public $controller = null;
    public $session = null;
    public $components = ['Auth'];

    /**
     * {@inheritDoc}
     */
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
     * @return string|null
     */
    public function saveAccessToken($account, $password)
    {
        $response = $this->sendResource('auth/login', 'post', [
            'account' => $account,
            'password' => $password,
        ]);
        if (isset($response['access_token']) && ($token = $response['access_token'])) {
            // セッションにトークンを書き込み
            $this->request->session()->write('access_token', $token);
            return $token;
        }
        return null;
    }

    /**
     * アクセストークンを破棄します。
     *
     * @return array|object
     */
    public function removeAccessToken()
    {
        return $this->sendResource('auth/logout', 'delete');
    }

    /**
     * APIをコールします。
     *
     * @param string $url
     * @param string $method
     * @param bool $data
     * @param bool $assoc
     * @return array|object
     */
    public function sendResource(string $url, string $method, $data = [], $assoc = true)
    {
        $http = new Client();
        $callMethod = strtolower($method);
        $token = $this->request->session()->read('access_token');
        $response = $http->$callMethod($this->__getApiUrl().$url, $data,
            $this->__getAuthorizationHeaders($token));

        // アプリログイン済みだが、APIが401なら再認証
        if ($response->getStatusCode() == 401 && $this->Auth->user()) {
            $userId = $this->Auth->user('userId');
            $password = $this->Auth->user('password');
            $newToken = $this->saveAccessToken($userId, $password);
            $response = $http->$callMethod($this->__getApiUrl().$url, $data,
                $this->__getAuthorizationHeaders($newToken));
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
     * @return string APIサーバのURL
     */
    private function __getApiUrl()
    {
        return env('AUTH_API_PATH', 'http://localhost/');
    }

    /**
     * 認可ヘッダを生成します。
     *
     * @return string APIサーバのURL
     */
    private function __getAuthorizationHeaders($token)
    {
        return [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ]
        ];
    }
}
