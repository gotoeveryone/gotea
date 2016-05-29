<?php

namespace App\Model\Entity;

/**
 * タイトルエンティティ
 */
class Title extends AppEntity
{
    /**
     * 配列の値をエンティティに保存します。
     * 
     * @param array $array
     */
    public function patchEntityWithArray(array $array)
    {
        // POSTされた値を設定
        $this->set('NAME', $array['titleName']);
        $this->set('NAME_ENGLISH', $array['titleNameEn']);
        $this->set('HOLDING', $array['holding']);
        $this->set('SORT_ORDER', $array['order']);
        $this->set('GROUP_FLAG', $array['groupFlag']);
        $this->set('HTML_FILE_NAME', $array['htmlFileName']);
        $this->set('HTML_FILE_MODIFIED', date($array['htmlModifyDate']));
        $this->set('DELETE_FLAG', $array['deleteFlag']);
    }
}
