<?php
declare(strict_types=1);

namespace Gotea\View;

use Cake\I18n\FrozenTime;
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
     * @inheritDoc
     */
    public function initialize(): void
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
        return $this->Identity->get('isAdmin');
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

    /**
     * 入段年の選択肢を取得する
     *
     * @return array
     */
    public function joinedYears()
    {
        $limit = FrozenTime::now()->addYears(1)->year;

        $options = [];
        for ($i = 1920; $i <= $limit; $i++) {
            $options[$i] = $i . '年';
        }

        return $options;
    }

    /**
     * 入段月の選択肢を取得する
     *
     * @return array
     */
    public function joinedMonths()
    {
        $options = [];
        for ($i = 1; $i <= 12; $i++) {
            $options[$i] = $i . '月';
        }

        return $options;
    }

    /**
     * 入段日の選択肢を取得する
     *
     * @return array
     */
    public function joinedDays()
    {
        $options = [];
        for ($i = 1; $i <= 31; $i++) {
            $options[$i] = $i . '日';
        }

        return $options;
    }
}
