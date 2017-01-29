<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

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
class TitleScore extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];

    /**
     * 勝者名を取得します。
     * 
     * @return string 勝者名
     */
    public function getWinner()
    {
        if (($detail = $this->win_detail) && ($winner = $detail->winner)) {
            return $winner->getNameWithRank();
        }
        return '';
    }

    /**
     * 敗者名を取得します。
     * 
     * @return string 敗者名
     */
    public function getLoser()
    {
        if (($detail = $this->lose_detail) && ($loser = $detail->loser)) {
            return $loser->getNameWithRank();
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
