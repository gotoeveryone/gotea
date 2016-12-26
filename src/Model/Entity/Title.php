<?php

namespace App\Model\Entity;

use Cake\ORM\TableRegistry;

/**
 * タイトルエンティティ
 */
class Title extends AppEntity
{
    /**
     * 保持履歴を取得します。
     * 
     * @param array $histories
     * @return array
     */
    protected function _getRetentionHistories($histories)
    {
        if (!$histories) {
            $tables = TableRegistry::get('RetentionHistories');
            $histories = $tables->find()
                    ->where(['title_id' => $this->id])->orderDesc('target_year')->all()->toArray();
        }
        return $histories;
    }
}
