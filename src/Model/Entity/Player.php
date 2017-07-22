<?php

namespace App\Model\Entity;

use Cake\Collection\Collection;
use Cake\I18n\Date;
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
     * 棋士の所属組織を取得します。
     *
     * @param mixed $value
     * @return Organization 所属組織
     */
    protected function _getOrganization($value)
    {
        if ($value) {
            return $value;
        }

        $result = TableRegistry::get('Organizations')->get($this->organization_id);
        return $this->organization = $result;
    }

    /**
     * 棋士の昇段情報を取得します。
     *
     * @param mixed $value
     * @return \Cake\ORM\ResultSet|null 昇段情報
     */
    protected function _getPlayerRanks($value)
    {
        if ($value) {
            return $value;
        }

        if (!$this->id) {
            return new Collection();
        }

        $result = TableRegistry::get('PlayerRanks')->findRanks($this->id);
        return $this->player_ranks = $result;
    }

    /**
     * 棋士の成績を取得します。
     *
     * @param mixed $value
     * @return \Cake\ORM\ResultSet|null 成績
     */
    protected function _getTitleScores($value)
    {
        if ($value) {
            return $value;
        }

        if (!$this->id) {
            return new Collection();
        }

        $result = TableRegistry::get('TitleScores')->findFromYear($this->id);
        return $this->title_scores = $result;
    }

    /**
     * 棋士の成績（旧取得方式）を取得します。
     *
     * @param mixed $value
     * @return \Cake\ORM\ResultSet|null 昇段情報
     */
    protected function _getOldScores($value)
    {
        if ($value) {
            return $value;
        }

        if (!$this->id) {
            return new Collection();
        }

        $result = TableRegistry::get('PlayerScores')->findByPlayerId($this->id)
            ->contain(['Ranks'])->orderDesc('target_year');
        return $this->old_scores = $result;
    }

    /**
     * 棋士のタイトル獲得履歴を取得します。
     *
     * @param mixed $value
     * @return \Cake\ORM\ResultSet|null タイトル獲得履歴
     */
    protected function _getRetentionHistories($value)
    {
        if ($value) {
            return $value;
        }

        if (!$this->id) {
            return null;
        }

        $result = TableRegistry::get('RetentionHistories')->findHistoriesByPlayer($this->id);
        return $this->retention_histories = $result;
    }

    /**
     * 誕生日を設定します。
     *
     * @param mixed $birthday
     * @return Date
     */
    protected function _setBirthday($birthday)
    {
        if ($birthday && !($birthday instanceof Date)) {
            return Date::parseDate($birthday, 'YYYY/MM/dd');
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
     * @return Collection
     */
    public function groupByYearFromHistories()
    {
        return $this->retention_histories->groupBy('target_year');
    }

    /**
     * ランキング表示用の名前を取得します。
     *
     * @param Country $country 検索対象の所属国
     * @param boolean $showJp 日本語で表示するか
     * @return string
     */
    public function getRankingName(Country $country, $showJp = false): string
    {
        // タイトル保持無しの棋戦は所属国を表示
        if (!$country->has_title) {
            if ($showJp) {
                return $this->name.'('.$this->country->name.')';
            }
            return $this->name_english.'('.$this->country->name_english.')';
        }

        // タイトル保持ありの棋戦
        if ($showJp) {
            return $this->name.' '.$this->rank->name;
        }
        return $this->name_english.'('.$this->rank->rank_numeric.' dan)';
    }

    /**
     * タイトル成績を表示する年の一覧を取得します。
     *
     * @return array 年の一覧
     */
    public function years(): array
    {
        $year = intval(Date::now()->year);
        // 引退棋士
        if ($this->is_retired) {
            if (!$this->retired) {
                return [];
            }
            // 前年以前に引退している場合は引退した年まで
            if ($year > $this->retired->year) {
                $year = $this->retired->year;
            }
        }

        $years = [];
        for ($i = $year; $i >= 2017; $i--) {
            $years[] = $i;
        }
        return $years;
    }

    /**
     * モデルのデータを出力用配列形式で返却します。
     *
     * @return array 配列
     */
    public function renderArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'nameEnglish' => $this->name_english,
            'sex' => $this->sex,
            'countryName' => $this->country ? $this->country->name : '',
            'rankId' => $this->rank->id,
            'rankName' => $this->rank ? $this->rank->name : '',
        ];
    }

    /**
     * 勝数を取得します。
     *
     * @param \Cake\ORM\ResultSet $scores
     * @param int|null $year
     * @param boolean $world
     * @return int|string 勝数
     */
    public function win($scores, $year = null, $world = false)
    {
        return $this->__show('win', $scores, $year, $world);
    }

    /**
     * 敗数を取得します。
     *
     * @param \Cake\ORM\ResultSet $scores
     * @param int|null $year
     * @param boolean $world
     * @return int|string 敗数
     */
    public function lose($scores, $year = null, $world = false)
    {
        return $this->__show('lose', $scores, $year, $world);
    }

    /**
     * 引分数を取得します。
     *
     * @param \Cake\ORM\ResultSet $scores
     * @param int|null $year
     * @param boolean $world
     * @return int|string 引分数
     */
    public function draw($scores, $year = null, $world = false)
    {
        return $this->__show('draw', $scores, $year, $world);
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
    private function __show($type, $scores, $year = null, $world = false)
    {
        if ($year === null) {
            $year = Date::now()->year;
        }
        $score = $scores->filter(function($item, $key) use ($year) {
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
