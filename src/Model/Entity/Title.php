<?php

namespace Gotea\Model\Entity;

use Cake\I18n\FrozenDate;

/**
 * タイトルエンティティ
 *
 * @property int $id
 * @property int $country_id
 * @property string $name
 * @property string $name_english
 * @property int $holding
 * @property int $sort_order
 * @property string $html_file_name
 * @property \Cake\I18n\FrozenDate $html_file_modified
 * @property string $remarks
 * @property bool $is_team
 * @property bool $is_closed
 * @property bool $is_output
 * @property \Cake\I18n\FrozenTime $created
 * @property string $created_by
 * @property \Cake\I18n\FrozenTime $modified
 * @property string $updated_by
 *
 * @property \Gotea\Model\Entity\Country $country
 * @property \Gotea\Model\Entity\RetentionHistory[] $retention_histories
 *
 * @property \Gotea\Model\Entity\RetentionHistory|null $now_retention
 * @property \Gotea\Model\Entity\RetentionHistory[] $histories
 */
class Title extends AppEntity
{
    use CountryTrait;

    /**
     * 現在の保持情報を取得します。
     *
     * @return \Gotea\Model\Entity\RetentionHistory|null
     */
    protected function _getNowRetention()
    {
        return collection($this->retention_histories)->filter(function ($item) {
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
        return collection($this->retention_histories)->filter(function ($item) {
            return $item->holding < $this->holding;
        });
    }

    /**
     * HTMLファイル修正日を設定します。
     *
     * @param mixed $newValue 更新値
     * @return \Cake\I18n\FrozenDate|null
     */
    protected function _setHtmlFileModified($newValue = null)
    {
        if ($newValue && !($newValue instanceof FrozenDate)) {
            return FrozenDate::parseDate($newValue, 'yyyy/MM/dd');
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
     * 新着の履歴かどうかを判定する。
     *
     * @return bool
     */
    public function isNewHistories(): bool
    {
        if (!($history = $this->now_retention)) {
            return false;
        }

        return $history->isRecent();
    }

    /**
     * 修正日が指定日以内かどうかを判定する。
     *
     * @return bool
     */
    public function isRecentModified(): bool
    {
        return $this->html_file_modified->wasWithinLast('1 months');
    }

    /**
     * モデルのデータを出力用配列形式で返却します。
     *
     * @return array 配列
     */
    public function toArray(): array
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
            'isOutput' => $this->is_output,
        ];
    }
}
