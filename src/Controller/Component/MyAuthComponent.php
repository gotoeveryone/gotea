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
    use ApiTrait;

    /**
     * ログイン処理を行います。
     *
     * @param array $credentials 認証情報
     * @return bool 認証成功すればtrue
     */
    public function login(array $credentials)
    {
        // 認証
        if ($this->authenticate($credentials)) {
            return true;
        }
        $this->response = $this->response->withStatus(401);

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function logout()
    {
        $token = $this->user('accessToken');
        if ($token) {
            $this->callApi('deauth', 'delete', [], [
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
    private function authenticate(array $credentials)
    {
        // トークン発行
        $response = $this->callApi('auth', 'post', $credentials);

        if ($response['status'] !== 200) {
            return false;
        }

        // ユーザ取得
        $token = $response['content']['accessToken'] ?? null;
        $response = $this->callApi('users', 'get', [], [
            'Authorization' => "Bearer ${token}",
        ]);

        if ($response['status'] !== 200) {
            return false;
        }

        // セッションにユーザを保存
        $user = $response['content'];
        $user['password'] = $credentials['password'];
        $user['accessToken'] = $token;
        $this->setUser($user);

        Log::info(__('User {0} is logged', $user['account']));

        return $user;
    }
}
