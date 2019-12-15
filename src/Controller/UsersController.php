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

        $this->Authentication->allowUnauthenticated(['index', 'login']);
    }

    /**
     * 初期表示処理
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function index()
    {
        // すでにログイン済みならリダイレクト
        if ($this->Authentication->getIdentity()) {
            return $this->redirect($this->getLoginRedirectUrl());
        }

        $this->viewBuilder()->setLayout('login');

        return $this->set('form', new LoginForm)->render();
    }

    /**
     * ログイン処理
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function login()
    {
        // ログイン
        $credentials = $this->getRequest()->getParsedBody();
        $form = new LoginForm();
        if (!$form->validate($credentials)) {
            return $this->setErrors(400, $form->getErrors())->setAction('index');
        }

        $result = $this->Authentication->getResult();

        // ログイン成功
        if ($result->isValid()) {
            // リダイレクト先をクエリパラメータに設定しておく
            $this->setRequest($this->getRequest()->withQueryParams([
                'redirect' => $this->getRequest()->getData('redirect'),
            ]));

            return $this->redirect($this->getLoginRedirectUrl());
        }

        // ログイン失敗
        return $this->setErrors(401, __('Authentication failed'))->setAction('index');
    }

    /**
     * ログアウト
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function logout()
    {
        return $this->redirect($this->Authentication->logout());
    }
}
