<?php

namespace App\Model\Entity;

use Cake\ORM\TableRegistry;

/**
 * タイトルエンティティ
 */
class Title extends AppEntity
{
    /**
     * タイトル獲得履歴を取得します。
     *
     * @param mixed $value
     * @return \Cake\ORM\ResultSet|null タイトル獲得履歴
     */
    protected function _getRetentionHistories($value)
    {
        if ($value) {
            return $value;
        }

        if (!$this->id) {
            return null;
        }

        $result = TableRegistry::get('RetentionHistories')->findHistoriesByTitle($this->id);
        return $this->retention_histories = $result;
    }

    /**
     * 現在の優勝者を取得します。
     *
     * @param boolean $isJp
     * @return string
     */
    public function getWinnerName($isJp = true): string
    {
        if (empty($this->retention_histories)) {
            return '';
        } else {
            $retention = $this->retention_histories[0];
            if ($this->is_team) {
                return $retention->win_group_name;
            } else if (!$isJp) {
                return "{$retention->player->name_english} ({$retention->rank->rank_numeric} dan)";
            } else {
                return "{$retention->player->name} {$retention->rank->name}";
            }
        }
    }

    /**
     * 保持者の最終登録日が指定日以内かどうかを判定する。
     *
     * @return boolean
     */
    public function isNewHistories() : bool
    {
        if (!$this->retention_histories) {
            return false;
        }
        return $this->retention_histories[0]->created->wasWithinLast(20);
    }

    /**
     * 修正日が指定日以内かどうかを判定する。
     *
     * @return boolean
     */
    public function isRecentModified() : bool
    {
        return $this->html_file_modified->wasWithinLast('1 months');
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
            'countryName' => $this->country ? $this->country->name : '',
            'holding' => $this->holding,
            'sortOrder' => $this->sort_order,
            'htmlFileName' => $this->html_file_name,
            'htmlFileModified' => $this->html_file_modified,
        ];
    }
}
