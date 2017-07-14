<?php
namespace App\Model\Entity;

/**
 * TitleScore Entity
 *
 * @property int $id
 * @property int $title_id
 * @property string $name
 * @property \Cake\I18n\Time $started
 * @property \Cake\I18n\Time $ended
 * @property bool $is_world
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 *
 * @property \App\Model\Entity\Title $title
 * @property \App\Model\Entity\TitleScoreDetail[] $title_score_details
 */
class TitleScore extends AppEntity
{
    /**
     * 勝者名を取得します。
     *
     * @param int|null 棋士ID
     * @return string 勝者名
     */
    public function getWinner($id = null)
    {
        if (($detail = $this->win_detail) && ($winner = $detail->winner)) {
            if ($winner->id == $id) {
                return '<span class="selected">'.h($winner->getNameWithRank()).'</span>';
            }
            return h($winner->getNameWithRank());
        }
        return '';
    }

    /**
     * 敗者名を取得します。
     *
     * @param int|null 棋士ID
     * @return string 敗者名
     */
    public function getLoser($id = null)
    {
        if (($detail = $this->lose_detail) && ($loser = $detail->loser)) {
            if ($loser->id == $id) {
                return '<span class="selected">'.h($loser->getNameWithRank()).'</span>';
            }
            return h($loser->getNameWithRank());
        }
        return '';
    }

    /**
     * 対局日を取得します。
     * 複数日にまたがった場合、from-toという表示になります。
     *
     * @return string
     */
    protected function _getDate()
    {
        return $this->started.($this->started == $this->ended ? '' : '-'.$this->ended->format('d'));
    }
}
