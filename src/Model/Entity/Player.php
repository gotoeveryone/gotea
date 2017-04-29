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
     * 成績情報を取得します。
     * 
     * @return object
     */
    protected function _getPlayerScores($playerScores)
    {
        if (!$playerScores) {
            $scores = TableRegistry::get('PlayerScores');
            $playerScores = $scores->find()
                    ->where(['player_id' => $this->id])->orderDesc('target_year')->all()->toArray();
        }
        return $playerScores;
    }

    /**
     * タイトル成績情報を取得します。
     * 
     * @return object
     */
    protected function _getTitleScores()
    {
        $scores = TableRegistry::get('TitleScores');
        return $scores->findFromYear($this->id);
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
     * 勝率を取得します。
     *
     * @param int $year
     * @param boolean $world
     * @return int 勝率（整数）
     */
    public function percent(int $year, $world = false)
    {
        $win = $this->win($year, $world);
        $lose = $this->lose($year, $world);
        $sum = $win + $lose;
        if (!$sum) {
            return 0;
        }
        return round($win / ($sum) * 100);
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
