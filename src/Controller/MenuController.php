<?php

namespace App\Controller;

use Cake\Event\Event;

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
	 */
    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);
        $this->set('cakeDescription', 'メニュー');
    }

    public function index() {
    }
}
