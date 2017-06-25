<?php

namespace App\Controller;

use Cake\Http\Response;

/**
 * メニュー用コントローラ
 *
 * @author  Kazuki Kamizuru
 * @since   2015/07/26
 */
class MenuController extends AppController
{
	/**
	 * 初期処理
     *
     * @return Response
	 */
    public function index() {
        $this->_setTitle('メニュー');
        return $this->render('index');
    }
}
