<?php

namespace App\Model\Entity;

use Cake\I18n\Date;

/**
 * 棋士エンティティ
 */
class Player extends AppEntity
{
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
    public function years()
    {
        $years = [];
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
        for ($i = $year; $i >= 2017; $i--) {
            $years[] = $i;
        }
        return $years;
    }

    /**
     * 棋士名と段位を取得します。
     *
     * @return string 棋士名 段位
     */
    public function getNameWithRank()
    {
        return $this->name.' '.$this->rank->name;
    }

    /**
     * 年齢を取得します。
     *
     * @return int|null 年齢
     */
    public function getAge()
    {
        if (!$this->birthday) {
            return null;
        }
        $now = new Date();
        return $now->diffInYears($this->birthday);
    }

    /**
     * 誕生日を設定します。
     *
     * @param type $birthday
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
     * @param type $joined
     * @return string
     */
    protected function _setJoined($joined)
    {
        return str_replace('-', '', str_replace('/', '', $joined));
    }

    /**
     * モデルのデータを出力用配列形式で返却します。
     *
     * @return array 配列
     */
    public function renderArray() : array
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
     * @param ResultSet $scores
     * @param int|null $year
     * @param boolean $world
     * @return int|string 勝数
     */
    public function win($scores, $year = null, $world = false)
    {
        return $this->show('win', $scores, $year, $world);
    }

    /**
     * 敗数を取得します。
     *
     * @param ResultSet $scores
     * @param int|null $year
     * @param boolean $world
     * @return int|string 敗数
     */
    public function lose($scores, $year = null, $world = false)
    {
        return $this->show('lose', $scores, $year, $world);
    }

    /**
     * 引分数を取得します。
     *
     * @param ResultSet $scores
     * @param int|null $year
     * @param boolean $world
     * @return int|string 引分数
     */
    public function draw($scores, $year = null, $world = false)
    {
        return $this->show('draw', $scores, $year, $world);
    }

    /**
     * 指定された成績の値を取得します。
     *
     * @param string $type
     * @param ResultSet $scores
     * @param int|null $year
     * @param bool $world
     * @return int|string 対象数
     */
    private function show($type, $scores, $year = null, $world = false)
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
