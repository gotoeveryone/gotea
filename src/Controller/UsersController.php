<?php

namespace App\Controller;

use App\Form\LoginForm;
use Cake\Event\Event;

/**
 * ログイン用コントローラ
 *
 * @author		Kazuki Kamizuru
 * @since		2015/07/26
 */
class UsersController extends AppController
{
    /**
     * 初期処理
     */
    public function initialize()
    {
        parent::initialize();
    }

	/**
	 * アクション実行前処理
     * 
     * @param $event
	 */
    public function beforeFilter(Event $event)
    {
		// すでにログイン済みならリダイレクト
        if ($this->Auth->user() && ($this->request->action !== 'logout')) {
			return $this->redirect($this->Auth->redirectUrl());
        }
        parent::beforeFilter($event);
        $this->Auth->allow(['index', 'login']);
    }

	/**
	 * 描画前処理
     * 
     * @param $event
	 */
    public function beforeRender(Event $event)
    {
        $this->_setTitle('ログイン');
        parent::beforeRender($event);
    }

    /**
     * 初期表示処理
     */
    public function index()
    {
        return $this->render('index');
    }

    /**
     * 初期表示処理
     */
    public function login()
    {
        $form = new LoginForm();
        if (!$form->validate($this->request->getParsedBody())) {
            $this->Flash->error($form->errors());
            return $this->setAction('index');
        }

        $account = $this->request->getData('username');
        $password = $this->request->getData('password');

        // ログイン
        if ($this->Auth->login($account, $password)) {
            // リダイレクト
            return $this->redirect($this->Auth->redirectUrl());
        }

        return $this->setAction('index');
    }

    /**
     * ログアウト
     * @return bool
     */
    public function logout()
    {
        return $this->redirect($this->Auth->logout());
    }
}
