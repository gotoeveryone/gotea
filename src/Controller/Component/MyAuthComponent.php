<?php

namespace App\Controller\Component;

use Cake\Controller\Component\AuthComponent;
use Cake\Validation\Validator;

/**
 * 独自認証用コンポーネント
 */
class MyAuthComponent extends AuthComponent
{
    public $controller = null;
    public $components = ['Json', 'Log', 'Flash'];

    public function initialize(array $config)
    {
        parent::initialize($config);

        /**
         * Get current controller
        */
        $this->controller = $this->_registry->getController();
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
}
