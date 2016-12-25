<?php

namespace App\Controller;

use Cake\Event\Event;
use Cake\Validation\Validator;

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
        $this->loadComponent('Json');
    }

	/**
	 * アクション実行前処理
     * 
     * @param $event
	 */
    public function beforeFilter(Event $event)
    {
		// すでにログイン済みならリダイレクト
        if ($this->MyAuth->user()) {
			return $this->redirect($this->MyAuth->redirectUrl());
        }
        parent::beforeFilter($event);
        $this->MyAuth->allow(['index', 'login']);
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
        $account = $this->request->data('username');
        $password = $this->request->data('password');

        // ログイン
        if ($this->MyAuth->login($account, $password)) {
            // リダイレクト
            return $this->redirect($this->MyAuth->redirectUrl());
        }

        return $this->index();
    }

    /**
     * ログアウト
     * @return bool
     */
    public function logout()
    {
        return $this->redirect($this->MyAuth->logout());
    }

    /**
     * ログイン時のバリデーションを行います。
     * 
     * @param $username
     * @param $password
     * @return array Array of invalid fields
     */
    private function __isValid($username, $password)
    {
        // 入力チェック
        $validator = new Validator();
        $validator
            ->notEmpty('username', 'ユーザIDは必須です。')
            ->notEmpty('password', 'パスワードは必須です。')
            ->add('username', [
                'length' => [
                    'rule' => ['maxLength', 10],
                    'message' => 'ユーザIDは10文字以下で入力してください。'
                ]
            ])
            ->add('password', [
                'length' => [
                    'rule' => ['maxLength', 10],
                    'message' => 'パスワードは10文字以下で入力してください。'
                ]
            ]
        );
        return $validator->errors(['username' => $username, 'password' => $password]);
    }
}
