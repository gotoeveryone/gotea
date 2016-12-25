<?php

namespace App\Controller\Component;

use Cake\Controller\Component\AuthComponent;
use Cake\Validation\Validator;

/**
 * 独自認証用コンポーネント
 */
class MyAuthComponent extends AuthComponent
{
    public $components = ['Json', 'Log', 'Flash'];

    public function initialize(array $config)
    {
        parent::initialize($config);
    }

    /**
     * ログイン処理を行います。
     * 
     * @param $account
     * @param $password
     * @return user object or false
     */
    public function login($account, $password)
    {
        // 入力チェック
        if (($errors = $this->__isValid($account, $password))) {
            $this->Log->error(__('ログイン失敗（バリデーション）'));
            $this->Flash->error($this->_getErrorMessage($errors));
            return false;
        }

        // トークンが保存できなければログインエラー
        if (!$this->Json->saveAccessToken($account, $password)) {
            $this->Log->error(__('ログイン失敗（認証）'));
            $this->Flash->error(__('認証に失敗しました。'));
            return false;
        }

        // ユーザの詳細情報を取得してセッションに設定
        $user = $this->Json->sendResource('users/detail', 'get');
        $user['password'] = $password;
        $this->setUser($user);
        $this->Log->info(__("ユーザ：{$user['userName']}がログインしました。"));

        return $user;
    }

    /**
     * ログアウト処理を行います。
     * 
     * @return string Normalized config `logoutRedirect`
     */
    public function logout()
    {
        // トークンの削除
        $this->Json->removeAccessToken();
        return parent::logout();
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
