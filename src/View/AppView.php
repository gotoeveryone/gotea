<?php

namespace Gotea\View;

use Cake\View\View;

/**
 * アプリケーション基底のビュー
 *
 * @property \Gotea\View\Helper\DateHelper $Date
 */
class AppView extends View
{
    /**
     * 認証済みかを判定
     *
     * @return bool
     */
    public function isAuth()
    {
        return (bool)$this->getUser();
    }

    /**
     * 管理者でログインしているかを判定
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->getUser('role', '一般') === '管理者';
    }

    /**
     * ユーザ情報を取得する。
     *
     * @param string|null $key キー
     * @param mixed $default デフォルト値
     * @return array ユーザ情報
     */
    public function getUser($key = null, $default = null)
    {
        $session = $this->getRequest()->getSession();
        if (!$session) {
            return $default;
        }

        if (!$key) {
            return $session->read('Auth.User');
        }

        return $session->read("Auth.User.${key}");
    }

    /**
     * ダイアログモードかどうか
     *
     * @return bool
     */
    public function isDialogMode()
    {
        return !empty($this->get('isDialog', false));
    }

    /**
     * タイトルを保持しているか
     *
     * @return bool
     */
    public function hasTitle()
    {
        return !empty($this->get('pageTitle', ''));
    }
}
