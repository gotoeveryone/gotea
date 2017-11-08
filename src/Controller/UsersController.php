<?php

namespace Gotea\Controller;

use Cake\Event\Event;
use Gotea\Form\LoginForm;

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

        return $this->set('form', new LoginForm)->_renderWith('ログイン');
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
        $credentials = $this->request->getParsedBody();
        $form = new LoginForm();
        if (!$form->validate($credentials)) {
            $this->Flash->error($form->errors());

            return $this->setAction('index');
        }

        // ログイン成功→リダイレクト
        if ($this->Auth->login($credentials)) {
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
