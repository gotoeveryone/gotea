<?php
namespace Gotea\View\Cell;

use Cake\I18n\Date;
use Cake\View\Cell;

/**
 * ナビゲーションに表示する情報
 */
class NavigationCell extends Cell
{
    /**
     * List of valid options that can be passed into this
     * cell's constructor.
     *
     * @var array
     */
    protected $_validCellOptions = [];

    /**
     * 直近1ヶ月以内に昇段した棋士の一覧を取得する。
     *
     * @return void
     */
    public function display()
    {
        $this->loadModel('PlayerRanks');
        $recents = $this->PlayerRanks->find()
            ->contain(['Players', 'Ranks'])
            ->where(['promoted >=' => Date::now()->addMonths(-1)])
            ->orderDesc('promoted')
            ->groupBy(function ($item) {
                return $item->promoted->format('Y/m/d');
            });
        $this->set('recents', $recents);
    }
}
