<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Network\Http\Client;

/**
 * JSON連携用コンポーネント
 */
class JsonComponent extends Component
{
    public $controller = null;
    public $session = null;
    public $components = ['Auth'];

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
        $token = $this->postJson("users/login", [
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
        return $this->deleteJson("users/logout", [
            "access_token" => $this->request->session()->read('access_token')
        ]);
    }

    /**
     * 指定URLよりJSONを取得します。
     * 
     * @param string $url
     * @return Array
     */
    public function getJson(string $url)
    {
        $http = new Client();
        $tokenArray = ["access_token" => $this->request->session()->read('access_token')];
        $response = $http->get($this->__getApiUrl().$url, $tokenArray, $this->__getCaArray());
        // アプリログイン済みだが、APIが401なら再認証
        if ($response->statusCode() == 401 && $this->Auth) {
            $userId = $this->Auth->user('userid');
            $password = $this->Auth->user('password');
            $tokenArray = ["access_token" => $this->saveAccessToken($userId, $password)];
            $response = $http->get($this->__getApiUrl().$url, $tokenArray, $this->__getCaArray());
        }
        $this->response->statusCode($response->statusCode());
        if ($response->isOk()) {
            return json_decode($response->body(), true);
        } else {
            return ["status" => $response->statusCode(), "message" => "GETエラー発生"];
        }
    }

    /**
     * 指定URLへデータをPOSTします。
     * 
     * @param string $url
     * @return Array
     */
    public function postJson(string $url, $data = [])
    {
        if (($token = $this->request->session()->read('access_token'))) {
            $data["access_token"] = $token;
        }
        $http = new Client();
        $response = $http->post($this->__getApiUrl().$url, $data, $this->__getCaArray());
        // アプリログイン済みだが、APIが401なら再認証
        if ($response->statusCode() == 401 && $this->Auth) {
            $userId = $this->Auth->user('userid');
            $password = $this->Auth->user('password');
            $data["access_token"] = $this->saveAccessToken($userId, $password);
            $response = $http->post($this->__getApiUrl().$url, $data, $this->__getCaArray());
        }
        $this->response->statusCode($response->statusCode());
        if ($response->isOk()) {
            return json_decode($response->body(), true);
        } else {
            $this->log('POSTエラー発生');
            return ["status" => $response->statusCode(), "message" => "POSTエラー発生"];
        }
    }

    /**
     * 指定URLへデータのDELETEを行います。
     * 
     * @param type $url
     * @return type
     */
    public function deleteJson($url, $data = [])
    {
        $http = new Client();
        $response = $http->delete($this->__getApiUrl().$url, $data, $this->__getCaArray());
        $json = (object) null;
        if ($response->isOk()) {
            $json = json_decode($response->body(), true);
        }
        $this->response->statusCode($response->statusCode());
        return $json;
    }

    /**
     * APIサーバのURLを取得します。
     * 
     * @return type
     */
    private function __getApiUrl()
    {
        $serverName = getenv('SERVER_NAME');
        return "https://{$serverName}/web-resource/";
    }

    /**
     * CA証明書の配列を生成して取得します。
     * 
     * @return type
     */
    private function __getCaArray()
    {
        return [
            'ssl_cafile' => getenv('SSL_CA_CRT')
        ];
    }
}
