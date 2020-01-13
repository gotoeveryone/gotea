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
     * テンプレートの拡張子
     * CakePHP 4.x からは php になるため、アップグレード時にこの記述は不要になる
     * https://book.cakephp.org/4/ja/appendices/4-0-migration-guide.html
     *
     * @var string
     */
    protected $_ext = '.php';

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
