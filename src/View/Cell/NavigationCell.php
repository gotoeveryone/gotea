<?php
declare(strict_types=1);

namespace Gotea\View\Cell;

use Cake\View\Cell;

/**
 * ナビゲーションに表示する情報
 *
 * @property \Gotea\Model\Table\PlayerRanksTable $PlayerRanks
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
        $recents = $this->PlayerRanks
            ->findRecentPromoted()
            ->reject(function ($item) {
                // 入段日と昇段日が同じ（＝入段時点の段位の）場合は除外
                return $item->player->joined === $item->promoted->format('Ymd');
            })
            ->groupBy(function ($item) {
                return $item->promoted->format('Y/m/d');
            });

        $this->set('recents', $recents);
    }
}
