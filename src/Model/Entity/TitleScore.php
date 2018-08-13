<?php
namespace Gotea\Model\Entity;

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
 * @property \Gotea\Model\Entity\Title $title
 * @property \Gotea\Model\Entity\Country $country
 * @property \Gotea\Model\Entity\TitleScoreDetail[] $title_score_details
 */
class TitleScore extends AppEntity
{
    use CountryTrait;

    /**
     * 指定した棋士に合致するかを判定します。
     *
     * @param \Gotea\Model\Entity\Player|null $player 棋士
     * @param string|null $id 棋士ID
     * @return 判定結果
     */
    public function isSelected($player, $id = null)
    {
        if (!$player || !$id) {
            return false;
        }

        return $player->id === (int)$id;
    }

    /**
     * 勝者名を取得します。
     *
     * @return string 勝者名
     */
    public function getWinnerName()
    {
        if (($detail = $this->win_detail)) {
            return $detail->player_name . ' ' . $detail->rank->name;
        }

        return '';
    }

    /**
     * 敗者名を取得します。
     *
     * @return string 敗者名
     */
    public function getLoserName()
    {
        if (($detail = $this->lose_detail)) {
            return $detail->player_name . ' ' . $detail->rank->name;
        }

        return '';
    }

    /**
     * 勝者を取得します。
     *
     * @return \Gotea\Model\Entity\TitleScoreDetail|null
     */
    protected function _getWinDetail()
    {
        return collection($this->title_score_details)->filter(function ($item) {
            return $item->division === '勝';
        })->first();
    }

    /**
     * 敗者を取得します。
     *
     * @return \Gotea\Model\Entity\TitleScoreDetail|null
     */
    protected function _getLoseDetail()
    {
        return collection($this->title_score_details)->filter(function ($item) {
            return $item->division === '敗';
        })->first();
    }

    /**
     * 勝者を取得します。
     *
     * @return \Gotea\Model\Entity\Player|null
     */
    protected function _getWinner()
    {
        return $this->win_detail ? $this->win_detail->player : null;
    }

    /**
     * 敗者を取得します。
     *
     * @return \Gotea\Model\Entity\Player|null
     */
    protected function _getLoser()
    {
        return $this->lose_detail ? $this->lose_detail->player : null;
    }

    /**
     * 対局日を取得します。
     * 複数日にまたがった場合、fromとtoを返却します。
     *
     * @return array
     */
    protected function _getDates()
    {
        $dates = [$this->started];
        if ($this->ended->diffInDays($this->started, false) < 0) {
            $dates[] = $this->ended;
        }

        return $dates;
    }
}
