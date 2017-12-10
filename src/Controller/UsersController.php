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
        $credentials = $this->request->getParsedBody();
        $form = new LoginForm();
        if (!$form->validate($credentials)) {
            return $this->_setErrors(400, $form->errors())->setAction('index');
        }

        // ログイン成功→リダイレクト
        if ($this->Auth->login($credentials)) {
            $redirect = $this->Auth::QUERY_STRING_REDIRECT;
            $this->request = $this->request->withQueryParams([
                $redirect => $this->request->getData($redirect),
            ]);

            return $this->redirect($this->Auth->redirectUrl());
        }

        // ログイン失敗
        return $this->_setErrors(401, __('認証に失敗しました。'))->setAction('index');
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
