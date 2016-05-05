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
     * Go NewsのJSONを取得します。
     * 
     * @return type
     */
    public function getNewsJson()
    {
        $url = "http://localhost/WebResource/api/igokisen/news/";
        return $this->getJson($url);
    }

    /**
     * ランキングのJSONを取得します。
     * 
     * @param type $country
     * @param type $year
     * @param type $rank
     * @return type
     */
    public function getRankingJson($country, $year, $rank)
    {
        $url = "http://localhost/WebResource/api/igokisen/ranking/{$country}/{$year}/{$rank}";
        return $this->getJson($url);
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
        return $json;
    }
}
