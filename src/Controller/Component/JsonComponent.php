<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Network\Http\Client;

class JsonComponent extends Component {

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
        $token = $this->postJson("{$this->__getApiUrl()}users/login", [
            "account" => $account,
            "password" => $password
        ]);
        if ($token) {
            // セッションにトークンを書き込み
            $this->request->session()->write('access_token', $token['access_token']);
            return $token['access_token'];
        }
        return $token;
    }

    /**
     * アクセストークンを破棄します。
     * 
     * @return type
     */
    public function removeAccessToken()
    {
        return $this->deleteJson("{$this->__getApiUrl()}users/logout", [
            "access_token" => $this->request->session()->read('access_token')
        ]);
    }

    /**
     * 名前をもとに棋士情報JSONを取得します。
     * 
     * @param $name
     * @return type
     */
    public function getPlayer($name)
    {
        return $this->getJson("{$this->__getApiUrl()}players?name={$name}");
    }

    /**
     * Go NewsのJSONを取得します。
     * 
     * @return type
     */
    public function getNews()
    {
        return $this->getJson("{$this->__getApiUrl()}titles/news");
    }

    /**
     * ランキングのJSONを取得します。
     * 
     * @param type $country
     * @param type $year
     * @param type $limit
     * @return type
     */
    public function getRanking($country, $year, $limit, $isJp = false)
    {
        $encode = urlencode($country);
        $get = "{$this->__getApiUrl()}players/ranking?country={$encode}&year={$year}&limit={$limit}".($isJp ? "&with=jp" : "");
        return $this->getJson($get);
    }

    /**
     * 指定URLよりJSONを取得します。
     * 
     * @param type $url
     * @return type
     */
    public function getJson($url)
    {
        $tokenArray = [
            "access_token" => $this->request->session()->read('access_token'),
        ];
        $http = new Client();
        $response = $http->get($url, $tokenArray, $this->__getCaArray());
        $json = (object) null;
        // 401なら再認証
        if ($response->statusCode() == 401) {
            $userId = $this->Auth->user('userid');
            $password = $this->Auth->user('password');
            $this->saveAccessToken($userId, $password);
            $response = $http->get($url, $tokenArray, $this->__getCaArray());
        }
        if ($response->isOk()) {
            $json = json_decode($response->body(), true);
        }
        $this->response->statusCode($response->statusCode());
        return $json;
    }

    /**
     * 指定URLへデータをPOSTします。
     * 
     * @param type $url
     * @return type
     */
    public function postJson($url, $data = [])
    {
        $http = new Client();
        $response = $http->post($url, $data, $this->__getCaArray());
        $json = (object) null;
        if ($response->isOk()) {
            $json = json_decode($response->body(), true);
        }
        $this->response->statusCode($response->statusCode());
        return $json;
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
        $response = $http->delete($url, $data, $this->__getCaArray());
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
    private function __getCaArray() {
        return [
            'ssl_cafile' => getenv('SSL_CA_CRT')
        ];
    }
}
