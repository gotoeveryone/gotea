<?php

namespace App\Controller;

use Cake\Cache\Cache;
use Cake\Event\Event;
use Psr\Log\LogLevel;

/**
 * メニュー用コントローラ
 *
 * @package		app.Controller
 * @author		Kazuki Kamizuru
 * @since		2015/07/26
 */
class MenuController extends AppController
{
	/**
	 * 描画前処理
     *
     * @param Event $event
	 */
    public function beforeRender(Event $event)
    {
        $this->_setTitle('メニュー');
        parent::beforeRender($event);
    }

    public function index() {
        return $this->render('index');
    }

    /**
     * キャッシュをクリアします。
     */
    public function clear() {
        $config_list = Cache::configured();
        $result = "";
        foreach ($config_list as $value) {
            $result = "{$result}<br>　{$value}";
            Cache::clear(false, $value);
        }
        $this->log(__("キャッシュがクリアされました。"), LogLevel::INFO);
        $this->Flash->info(__("キャッシュをクリアしました。<br><br>【対象】{$result}"));
        return $this->index();
    }
}
