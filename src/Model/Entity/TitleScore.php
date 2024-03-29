<?php
declare(strict_types=1);

namespace Gotea\Model\Entity;

/**
 * TitleScore Entity
 *
 * @property int $id
 * @property int $title_id
 * @property string $name
 * @property string|null $result
 * @property \Cake\I18n\FrozenDate $started
 * @property \Cake\I18n\FrozenDate $ended
 * @property bool $is_world
 * @property bool $is_official
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \Gotea\Model\Entity\Title $title
 * @property \Gotea\Model\Entity\Country $country
 * @property \Gotea\Model\Entity\TitleScoreDetail[] $title_score_details
 *
 * @property int|null $winner_id
 * @property int|null $loser_id
 * @property string $winner_name
 * @property string $loser_name
 * @property \Gotea\Model\Entity\TitleScoreDetail $win_detail
 * @property \Gotea\Model\Entity\TitleScoreDetail $lose_detail
 * @property \Gotea\Model\Entity\Player|null $winner
 * @property \Gotea\Model\Entity\Player|null $loser
 * @property array $game_dates
 * @property bool $is_same_started
 */
class TitleScore extends AppEntity
{
    use CountryTrait;

    /**
     * 勝者の棋士IDを取得します。
     *
     * @return int|null 勝者の棋士ID
     */
    protected function _getWinnerId(): ?int
    {
        $detail = $this->win_detail;
        if ($detail) {
            return $detail->player_id;
        }

        return null;
    }

    /**
     * 敗者の棋士IDを取得します。
     *
     * @return int|null 敗者の棋士ID
     */
    protected function _getLoserId(): ?int
    {
        $detail = $this->lose_detail;
        if ($detail) {
            return $detail->player_id;
        }

        return null;
    }

    /**
     * 勝者名を取得します。
     *
     * @return string 勝者名
     */
    protected function _getWinnerName(): string
    {
        $detail = $this->win_detail;
        if ($detail) {
            return $detail->getPlayerNameWithRank($this->started);
        }

        return '';
    }

    /**
     * 敗者名を取得します。
     *
     * @return string 敗者名
     */
    protected function _getLoserName(): string
    {
        $detail = $this->lose_detail;
        if ($detail) {
            return $detail->getPlayerNameWithRank($this->started);
        }

        return '';
    }

    /**
     * 勝者を取得します。
     *
     * @return \Gotea\Model\Entity\TitleScoreDetail|null
     */
    protected function _getWinDetail(): ?TitleScoreDetail
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
    protected function _getLoseDetail(): ?TitleScoreDetail
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
    protected function _getWinner(): ?Player
    {
        return $this->win_detail ? $this->win_detail->player : null;
    }

    /**
     * 敗者を取得します。
     *
     * @return \Gotea\Model\Entity\Player|null
     */
    protected function _getLoser(): ?Player
    {
        return $this->lose_detail ? $this->lose_detail->player : null;
    }

    /**
     * 対局日を配列形式で取得します。
     * 開始日と終了日が異なる場合は2件、それ以外は1件のデータが返却されます。
     *
     * @return array
     */
    protected function _getGameDates(): array
    {
        $dates = [$this->started];
        if ($this->ended->diffInDays($this->started, false) < 0) {
            $dates[] = $this->ended;
        }

        return $dates;
    }

    /**
     * 開始日と終了日が同じ値かどうかを判定します。
     *
     * @return bool
     */
    protected function _getIsSameStarted(): bool
    {
        return $this->started->diffInDays($this->ended) === 0;
    }

    /**
     * 指定した棋士に合致するかを判定します。
     *
     * @param \Gotea\Model\Entity\Player|null $player 棋士
     * @param int|null $id 棋士ID
     * @return bool
     */
    public function isSelected(?Player $player, ?int $id): bool
    {
        if (!$player || !$id) {
            return false;
        }

        return $player->id === (int)$id;
    }
}
