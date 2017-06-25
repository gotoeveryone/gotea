<?php

namespace App\Controller;

use Cake\Event\Event;
use Cake\Http\Response;
use App\Form\LoginForm;

/**
 * ログイン用コントローラ
 *
 * @author		Kazuki Kamizuru
 * @since		2015/07/26
 */
class UsersController extends AppController
{
	/**
     * {@inheritDoc}
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
     * 初期表示処理
     *
     * @return Response
     */
    public function index()
    {
        $this->_setTitle('ログイン');
        return $this->render();
    }

    /**
     * ログイン処理
     *
     * @return Response
     */
    public function login()
    {
        // POSTのみ許可
        $this->request->allowMethod(['post']);

        // ログイン
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
     *
     * @return Response
     */
    public function logout()
    {
        $this->request->session()->destroy();
        return $this->redirect($this->Auth->logout());
    }
}
