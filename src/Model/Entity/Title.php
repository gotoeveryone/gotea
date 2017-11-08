<?php

namespace Gotea\Model\Entity;

use Cake\I18n\FrozenDate;
use Cake\ORM\TableRegistry;

/**
 * タイトルエンティティ
 */
class Title extends AppEntity
{
    /**
     * タイトル獲得履歴を取得します。
     *
     * @param mixed $value 更新値
     * @return mixed タイトル獲得履歴
     */
    protected function _getRetentionHistories($value)
    {
        if ($value) {
            return $value;
        }

        if (!$this->id) {
            return collection([]);
        }

        $result = TableRegistry::get('RetentionHistories')->findHistoriesByTitle($this->id);

        return $this->retention_histories = $result;
    }

    /**
     * 現在の保持情報を取得します。
     *
     * @return \Gotea\Model\Entity\RetentionHistory|null
     */
    protected function _getNowRetention()
    {
        return $this->retention_histories->filter(function ($item, $key) {
            return $item->holding === $this->holding;
        })->first();
    }

    /**
     * 前期以前の取得履歴を取得します。
     *
     * @return \Cake\Collection\Collection
     */
    protected function _getHistories()
    {
        return $this->retention_histories->filter(function ($item, $key) {
            return $item->holding < $this->holding;
        });
    }

    /**
     * HTMLファイル修正日を設定します。
     *
     * @param mixed $newValue 更新値
     * @return \Cake\I18n\FrozenDate
     */
    protected function _setHtmlFileModified($newValue)
    {
        if ($newValue && !($newValue instanceof FrozenDate)) {
            return FrozenDate::parseDate($newValue, 'YYYY/MM/dd');
        }

        return $newValue;
    }

    /**
     * 現在の優勝者を取得します。
     *
     * @param bool $isJp 日本名を取得するか
     * @return string|null
     */
    public function getWinnerName($isJp = true)
    {
        if (!($history = $this->now_retention)) {
            return null;
        }

        if ($this->is_team) {
            return $history->win_group_name;
        } elseif (!$isJp) {
            return "{$history->player->name_english} ({$history->rank->rank_numeric} dan)";
        } else {
            return "{$history->player->name} {$history->rank->name}";
        }
    }

    /**
     * 保持者の最終登録日が指定日以内かどうかを判定する。
     *
     * @return bool
     */
    public function isNewHistories() : bool
    {
        if (!($history = $this->now_retention)) {
            return false;
        }

        return $history->created->wasWithinLast(20);
    }

    /**
     * 修正日が指定日以内かどうかを判定する。
     *
     * @return bool
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
    public function toArray() : array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'nameEnglish' => $this->name_english,
            'countryName' => $this->country ? $this->country->name : '',
            'holding' => $this->holding,
            'sortOrder' => $this->sort_order,
            'isTeam' => $this->is_team,
            'htmlFileName' => $this->html_file_name,
            'htmlFileModified' => $this->html_file_modified->format('Y/m/d'),
            'isClosed' => $this->is_closed,
        ];
    }
}
