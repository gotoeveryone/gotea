<?php
declare(strict_types=1);

namespace Gotea\View\Cell;

use Cake\View\Cell;

/**
 * ナビゲーションに表示する情報
 */
class NavigationCell extends Cell
{
    /**
     * @inheritDoc
     */
    protected array $_validCellOptions = [];

    /**
     * 直近1ヶ月以内に昇段した棋士の一覧を取得する。
     *
     * @return void
     */
    public function display(): void
    {
        /** @var \Gotea\Model\Table\PlayerRanksTable $table */
        $table = $this->fetchTable('PlayerRanks');
        $recents = $table
            ->findRecentPromoted()
            ->all()
            ->reject(function ($item) {
                // 入段日と昇段日が同じ（＝入段時点の段位の）場合は除外
                return $item->player->joined_ymd === $item->promoted->format('Ymd');
            })
            ->groupBy(function ($item) {
                return $item->promoted->format('Y/m/d');
            });

        $this->set('recents', $recents);
    }
}
