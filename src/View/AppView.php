<?php

namespace Gotea\View;

use Cake\View\View;

/**
 * アプリケーション基底のビュー
 *
 * @property \Authentication\View\Helper\IdentityHelper $Identity
 * @property \Gotea\View\Helper\DateHelper $Date
 */
class AppView extends View
{
    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadHelper('Authentication.Identity');
    }

    /**
     * 管理者でログインしているかを判定
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->Identity->get('role') === '管理者';
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
