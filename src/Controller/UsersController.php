<?php

namespace App\Controller;

use Cake\Event\Event;
use App\Form\LoginForm;

/**
 * ログイン用コントローラ
 *
 * @author      Kazuki Kamizuru
 * @since       2015/07/26
 */
class UsersController extends AppController
{
    /**
     * {@inheritDoc}
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['index', 'login']);
    }

    /**
     * 初期表示処理
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function index()
    {
        // すでにログイン済みならリダイレクト
        if ($this->Auth->user()) {
            return $this->redirect($this->Auth->redirectUrl());
        }
        return $this->_setTitle('ログイン')->render();
    }

    /**
     * ログイン処理
     *
     * @return \Psr\Http\Message\ResponseInterface
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

        // ログイン成功→リダイレクト
        if ($this->Auth->login($account, $password)) {
            return $this->redirect($this->Auth->redirectUrl());
        }

        // ログイン失敗
        $this->Flash->error(__('認証に失敗しました。'));
        return $this->setAction('index');
    }

    /**
     * ログアウト
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function logout()
    {
        return $this->redirect($this->Auth->logout());
    }
}
