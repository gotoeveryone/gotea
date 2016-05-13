<?php

namespace App\Model\Entity;

use Cake\Network\Request;
use Cake\ORM\TableRegistry;
use Cake\I18n\Time;

/**
 * 棋士マスタエンティティ
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
        if (!$this->BIRTHDAY) {
            return null;
        }
        $time = new Time($this->BIRTHDAY);
        return $time->format(($format) ? $format : 'Y/m/d');
    }

    /**
     * 棋士名を設定します。
     * 
     * @param $name
     */
    public function setName($name)
    {
        $this->set('NAME', $name);
    }

    /**
     * 棋士名（英語）を設定します。
     * 
     * @param $nameEnglish
     */
    public function setNameEnglish($nameEnglish)
    {
        $this->set('NAME_ENGLISH', (empty($nameEnglish) ? null : $nameEnglish));
    }

    /**
     * 棋士名（その他）を設定します。
     * 
     * @param $nameOther
     */
    public function setNameOther($nameOther)
    {
        $this->set('NAME_OTHER', (empty($nameOther) ? null : $nameOther));
    }

    /**
     * 性別を設定します。
     * 
     * @param $sex
     */
    public function setSex($sex)
    {
        $this->set('SEX', $sex);
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
    public function setBirthday($birthday)
    {
        $time = new Time();
        $this->set('BIRTHDAY', (empty($birthday) ? null : $time->parseDate($birthday, 'YYYY/MM/dd')));
    }

    /**
     * 入段日を設定します。
     * 
     * @param type $enrollment
     */
    public function setEnrollment($enrollment)
    {
        $this->set('ENROLLMENT', (empty($enrollment) ? null : str_replace('/', '', $enrollment)));
    }

    /**
     * リクエストの値をエンティティに保存します。
     * 
     * @param Request $request
     */
    public function patchEntity(Request $request)
    {
        // 棋士名
        $this->set('NAME', $request->data('playerName'));
        // 棋士名（英語）
		$nameEnglish = $request->data('playerNameEn');
		$this->set('NAME_ENGLISH', (empty($nameEnglish) ? null : $nameEnglish));
        // 棋士名（その他）
		$nameOther = $request->data('playerNameOther');
		$this->set('NAME_OTHER', (empty($nameOther) ? null : $nameOther));
        // 所属国
        $this->setCountry($request->data('selectCountry'));
        // 段位
		$this->setRank($request->data('rank'));
        // 性別
		$this->set('SEX', $request->data('sex'));
        // 入段日
        $this->setEnrollment($request->data('enrollment'));
        // 誕生日
        $this->setBirthday($request->data('birthday'));
        // 所属組織
		$affiliation = $request->data('affiliation');
		$this->set('AFFILIATION', (empty($affiliation) ? null : $affiliation));
        // 引退フラグ
		$this->set('DELETE_FLAG', $request->data('retireFlag'));
    }
}
