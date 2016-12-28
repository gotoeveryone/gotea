<?php

namespace App\Controller;

use Cake\Event\Event;

/**
 * メニュー用コントローラ
 *
 * @author  Kazuki Kamizuru
 * @since   2015/07/26
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

	/**
	 * 初期処理
	 */
    public function index() {
        return $this->render('index');
    }
}
