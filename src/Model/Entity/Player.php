<?php

namespace App\Model\Entity;

use Cake\Network\Request;
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
     * @return App\Model\Entity\PlayerScore
     */
    protected function _getPlayerScores()
    {
        if (!isset($this->_virtual['player_scores'])) {
            $scores = TableRegistry::get('PlayerScores');
            $this->_virtual['player_scores'] = $scores->find()
                    ->where(['player_id' => $this->id])->orderDesc('target_year')->all();
        }
        return $this->_virtual['player_scores'];
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
