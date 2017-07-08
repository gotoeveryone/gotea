<?php

namespace App\Model\Entity;

use Cake\ORM\TableRegistry;
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
        $nowYear = intval(Date::now()->year);
        for ($i = $nowYear; $i >= 2017; $i--) {
            $years[] = $i;
        }
        return $years;
    }

    /**
     * 当年のタイトル成績情報を取得します。
     *
     * @return object
     */
    public function getTitleScoresNowYear()
    {
        return $this->getTitleScores(intval(Date::now()->year));
    }

    /**
     * タイトル成績情報を取得します。
     *
     * @param int $year
     * @return object
     */
    public function getTitleScores(int $year)
    {
        $scores = TableRegistry::get('TitleScores');
        return $scores->findFromYear($this->id, $year);
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
     * @param int $year
     * @param boolean $world
     * @return int 勝数
     */
    public function win(int $year, $world = false)
    {
        $details = ($world ? $this->world_win_details : $this->win_details);
        return $this->calc($year, $details);
    }

    /**
     * 敗数を取得します。
     *
     * @param int $year
     * @param boolean $world
     * @return int 敗数
     */
    public function lose(int $year, $world = false)
    {
        $details = ($world ? $this->world_lose_details : $this->lose_details);
        return $this->calc($year, $details);
    }

    /**
     * 引分数を取得します。
     *
     * @param int $year
     * @param boolean $world
     * @return int 引分数
     */
    public function draw(int $year, $world = false)
    {
        $details = ($world ? $this->world_draw_details : $this->draw_details);
        return $this->calc($year, $details);
    }

    /**
     * 対象数を取得します。
     *
     * @param int year
     * @param array details
     * @return int 対象数
     */
    private function calc(int $year, array $details)
    {
        foreach ($details as $detail) {
            if ((int) $detail->year === $year) {
                return $detail->cnt;
            }
        }
        return 0;
    }
}
