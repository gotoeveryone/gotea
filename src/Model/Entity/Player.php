<?php

namespace App\Model\Entity;

use Cake\I18n\FrozenDate;
use Cake\ORM\TableRegistry;

/**
 * 棋士エンティティ
 */
class Player extends AppEntity
{
    /**
     * 年齢を取得します。
     *
     * @return int|null 年齢
     */
    protected function _getAge()
    {
        return $this->birthday ? $this->birthday->age : null;
    }

    /**
     * 棋士名と段位を取得します。
     *
     * @return string 棋士名 段位
     */
    protected function _getNameWithRank()
    {
        return $this->name.' '.$this->rank->name;
    }

    /**
     * 棋士の昇段情報を取得します。
     *
     * @param mixed $value
     * @return \Cake\Collection\CollectionInterface 昇段情報
     */
    protected function _getPlayerRanks($value)
    {
        if ($value) {
            return $value;
        }

        if (!$this->id) {
            return collection([]);
        }

        $result = TableRegistry::get('PlayerRanks')->findRanks($this->id);
        return $this->player_ranks = $result;
    }

    /**
     * 棋士の成績（旧取得方式）を取得します。
     *
     * @param mixed $value
     * @return \Cake\Collection\CollectionInterface 昇段情報
     */
    protected function _getOldScores($value)
    {
        if ($value) {
            return $value;
        }

        if (!$this->id) {
            return collection([]);
        }

        $result = TableRegistry::get('PlayerScores')->findDescYears($this->id);
        return $this->old_scores = $result;
    }

    /**
     * 棋士のタイトル獲得履歴を取得します。
     *
     * @param mixed $value
     * @return \Cake\Collection\CollectionInterface タイトル獲得履歴
     */
    protected function _getRetentionHistories($value)
    {
        if ($value) {
            return $value;
        }

        if (!$this->id) {
            return collection([]);
        }

        $result = TableRegistry::get('RetentionHistories')->findHistoriesByPlayer($this->id);
        return $this->retention_histories = $result;
    }

    /**
     * 誕生日を設定します。
     *
     * @param mixed $birthday
     * @return FrozenDate
     */
    protected function _setBirthday($birthday)
    {
        if ($birthday && !($birthday instanceof FrozenDate)) {
            return FrozenDate::parseDate($birthday, 'YYYY/MM/dd');
        }
        return $birthday;
    }

    /**
     * 入段日を設定します。
     *
     * @param mixed $joined
     * @return string
     */
    protected function _setJoined($joined)
    {
        return str_replace(['-', '/'], '', $joined);
    }

    /**
     * 年度単位でグループ化します。
     *
     * @return Cake\Collection\Collection
     */
    public function groupByYearFromHistories()
    {
        return $this->retention_histories->groupBy('target_year');
    }

    /**
     * モデルのデータを出力用配列形式で返却します。
     *
     * @return array 配列
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'nameEnglish' => $this->name_english,
            'nameOther' => $this->name_other,
            'sex' => $this->sex,
            'birthday' => $this->birthday ? $this->birthday->format('Y/m/d') : null,
            'countryName' => $this->country->name,
            'rankId' => $this->rank->id,
            'rankName' => $this->rank->name,
            'isRetired' => $this->is_retired,
            'retired' => $this->retired,
        ];
    }

    /**
     * 勝数を取得します。
     *
     * @param int|null $year
     * @param boolean $world
     * @return int|string 勝数
     */
    public function win($year = null, $world = false)
    {
        return $this->__show('win', $year, $world);
    }

    /**
     * 敗数を取得します。
     *
     * @param int|null $year
     * @param boolean $world
     * @return int|string 敗数
     */
    public function lose($year = null, $world = false)
    {
        return $this->__show('lose', $year, $world);
    }

    /**
     * 引分数を取得します。
     *
     * @param int|null $year
     * @param boolean $world
     * @return int|string 引分数
     */
    public function draw($year = null, $world = false)
    {
        return $this->__show('draw', $year, $world);
    }

    /**
     * 指定された成績の値を取得します。
     *
     * @param string $type
     * @param \Cake\ORM\ResultSet $scores
     * @param int|null $year
     * @param bool $world
     * @return int|string 対象数
     */
    private function __show($type, $year = null, $world = false)
    {
        if ($year === null) {
            $year = FrozenDate::now()->year;
        }
        $scores = collection($this->title_score_details);
        $score = $scores->filter(function ($item, $key) use ($year) {
            return (int) $item->player_id === $this->id
                && (int) $item->target_year === $year;
        })->first();

        // 該当年の対局がない
        if (!$score) {
            // 前年以前に引退している場合は'-'固定
            if ($this->is_retired && (!$this->retired || $year > $this->retired->year)) {
                return '-';
            }
            return 0;
        }

        $propertyName = ($world) ? "${type}_point_world" : "${type}_point";
        return (int) $score->$propertyName;
    }
}
