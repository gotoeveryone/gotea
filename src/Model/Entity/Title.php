<?php

namespace App\Model\Entity;

use Cake\ORM\TableRegistry;

/**
 * タイトルエンティティ
 */
class Title extends AppEntity
{
    /**
     * 所属国を設定します。
     * 
     * @param $countryId
     */
    public function setCountry($countryId)
    {
        $countries = TableRegistry::get('Countries');
        $this->country = $countries->get($countryId);
    }

    /**
     * 配列の値をエンティティに保存します。
     * 
     * @param array $array
     */
    public function setFromArray(array $array)
    {
        // POSTされた値を設定
        $this->name = $array['titleName'];
        $this->name_english = $array['titleNameEn'];
        $this->holding = $array['holding'];
        $this->sort_order = $array['order'];
        $this->is_team = $array['groupFlag'];
        $this->html_file_name = $array['htmlFileName'];
        $this->html_file_modified = date($array['htmlModifyDate']);
        $this->is_closed = $array['deleteFlag'];
    }
}
