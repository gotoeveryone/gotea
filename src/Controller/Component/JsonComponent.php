<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Network\Http\Client;

class JsonComponent extends Component {

    public $controller = null;
    public $session = null;
    // WebAPIのURL
    private $apiUrl = "http://localhost/web-resource/";

    public function initialize(array $config)
    {
        parent::initialize($config);

        /**
         * Get current controller
        */
        $this->controller = $this->_registry->getController();
    }

    /**
     * 名前をもとに棋士情報JSONを取得します。
     * 
     * @param $name
     * @return type
     */
    public function getPlayer($name)
    {
        return $this->getJson($this->apiUrl."players?name={$name}");
    }

    /**
     * Go NewsのJSONを取得します。
     * 
     * @return type
     */
    public function getNews()
    {
        return $this->getJson($this->apiUrl."titles/news");
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
        $get = $this->apiUrl."players/ranking?country={$encode}&year={$year}&limit={$limit}".($isJp ? "&with=jp" : "");
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
        $response = $http->get($url);
        $json = (object) null;
        if ($response->isOk()) {
            $json = json_decode($response->body(), true);
        }
        $this->response->statusCode($response->statusCode());
        return $json;
    }
}
