<?php

namespace Gotea\Controller\Component;

use Cake\Controller\Component\AuthComponent;
use Cake\Log\Log;

/**
 * 独自認証用コンポーネント
 *
 * @author  Kazuki Kamizuru
 * @since   2016/12/25
 */
class MyAuthComponent extends AuthComponent
{
    public $components = ['Json', 'Flash'];

    /**
     * ログイン処理を行います。
     *
     * @param array $credentials 認証情報
     * @return array|false 認証に成功すればそのオブジェクト、失敗すればfalse
     */
    public function login(array $credentials)
    {
        // トークンが保存できなければログインエラー
        if (!($user = $this->__authenticate($credentials))) {
            Log::error(__('ログイン失敗（認証）'));

            return false;
        }

        Log::info(__("ユーザ：{$user['name']}がログインしました。"));

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function logout()
    {
        $token = $this->user('accessToken');
        if ($token) {
            $this->Json->callApi('deauth', 'delete', [], [
                'Authorization' => "Bearer ${token}",
            ]);
        }

        return parent::logout();
    }

    /**
     * 認証を行います。
     *
     * @param array $credentials 認証情報
     * @return array|false 認証に成功すればそのオブジェクト、失敗すればfalse
     */
    private function __authenticate(array $credentials)
    {
        // トークン発行
        $response = $this->Json->callApi('auth', 'post', $credentials);
        $token = $response['accessToken'] ?? null;

        if ($this->response->getStatusCode() !== 200) {
            return false;
        }

        // ユーザ取得
        $user = $this->Json->callApi('users', 'get', [], [
            'Authorization' => "Bearer ${token}",
        ]);

        if ($this->response->getStatusCode() !== 200) {
            return false;
        }

        // セッションにユーザを保存
        $user['password'] = $credentials['password'];
        $user['accessToken'] = $token;
        $this->setUser($user);

        return $user;
    }
}
