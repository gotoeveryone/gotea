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
}
