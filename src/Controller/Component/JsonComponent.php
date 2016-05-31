<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Network\Http\Client;

class JsonComponent extends Component {

    public $controller = null;
    public $session = null;

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
        $token = $this->postJson($this->__getApiUrl()."users/login", [
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
        $token = $this->request->session()->read('access_token');
        return $this->deleteJson($this->__getApiUrl()."users/logout", [
            "access_token" => $token
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
        return $this->getJson($this->__getApiUrl()."players?name={$name}");
    }

    /**
     * Go NewsのJSONを取得します。
     * 
     * @return type
     */
    public function getNews()
    {
        return $this->getJson($this->__getApiUrl()."titles/news");
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
        $get = $this->__getApiUrl()."players/ranking?country={$encode}&year={$year}&limit={$limit}".($isJp ? "&with=jp" : "");
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
        $http = new Client();
        $response = $http->get($url, [
            "access_token" => $this->request->session()->read('access_token'),
        ], [
            'ssl_cafile' => getenv('SSL_CA_CRT')
        ]);
        $json = (object) null;
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
        $response = $http->post($url, $data, [
            'ssl_cafile' => getenv('SSL_CA_CRT')
        ]);
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
        $response = $http->delete($url, $data, [
            'ssl_cafile' => getenv('SSL_CA_CRT')
        ]);
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
        return 'https://'.getenv('SERVER_NAME')."/web-resource/";
    }
}
