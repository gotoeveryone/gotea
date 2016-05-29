<?php

namespace App\Model\Entity;

use Cake\Network\Request;
use Cake\ORM\TableRegistry;
use Cake\I18n\Time;

/**
 * 棋士エンティティ
 */
class Player extends AppEntity
{
    /**
     * 誕生日を取得します。
     * 
     * @param type $format
     * @return type
     */
    public function getBirthday($format = null)
    {
        if (!$this->birthday) {
            return null;
        }
        $time = new Time($this->birthday);
        return $time->format(($format) ? $format : 'Y/m/d');
    }

    /**
     * 入段日を取得します。
     * 
     * @param type $format
     * @return type
     */
    public function getJoined($format = null)
    {
        if (!$this->joined) {
            return null;
        }
        $time = new Time($this->joined);
        return $time->format(($format) ? $format : 'Y/m/d');
    }

    /**
     * 所属国を設定します。
     * 
     * @param $countryId
     */
    public function setCountry($countryId)
    {        
        $countries = TableRegistry::get('Countries');
        $this->set('country', $countries->get($countryId));
    }

    /**
     * 段位を設定します。
     * 
     * @param $rankId
     */
    public function setRank($rankId)
    {
        $ranks = TableRegistry::get('Ranks');
        $this->set('rank', $ranks->get($rankId));
    }

    /**
     * 誕生日を設定します。
     * 
     * @param type $birthday
     */
    protected function _setBirthday($birthday)
    {
        $time = new Time();
        return (empty($birthday) ? null : $time->parseDate($birthday, 'YYYY/MM/dd'));
    }

    /**
     * 入段日を設定します。
     * 
     * @param type $joined
     */
    protected function _setJoined($joined)
    {
        $time = new Time();
        return (empty($joined) ? null : $time->parseDate($joined, 'YYYY/MM/dd'));
    }

    /**
     * リクエストの値をエンティティに保存します。
     * 
     * @param Request $request
     */
    public function setFromRequest(Request $request)
    {
        // 棋士名
        $this->name = $request->data('playerName');
        // 棋士名（英語）
		$nameEnglish = $request->data('playerNameEnglish');
		$this->name_english = (empty($nameEnglish) ? null : $nameEnglish);
        // 棋士名（その他）
		$nameOther = $request->data('playerNameOther');
		$this->name_other = (empty($nameOther) ? null : $nameOther);
        // 所属国
        $this->setCountry($request->data('selectCountry'));
        // 段位
		$this->setRank($request->data('rank'));
        // 性別
		$this->sex = $request->data('sex');
        // 入段日
        $this->joined = $request->data('joined');
        // 誕生日
        $this->birthday = $request->data('birthday');
        // 所属組織
		$organization = $request->data('organization');
		$this->organization = (empty($organization) ? null : $organization);
        // 引退フラグ
		$this->is_retired = $request->data('retired');
        // その他備考
		$this->remarks = $request->data('remarks');
    }
}
