<?php

namespace Gotea\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

/**
 * アプリケーションの共通エンティティ
 */
class AppEntity extends Entity
{
    // アクセス許可
    protected $_accessible = [
        '*' => true,
        'id' => false,
        'created' => false,
        'modified' => false,
    ];

    // 出力しないフィールド
    protected $_hidden = [
        'created',
        'created_by',
        'modified',
        'modified_by',
    ];

    /**
     * {@inheritDoc}
     */
    // public function &get($property)
    // {
    //     $value = parent::get($property);
    //     // 配列ならCollectionに変換
    //     if (is_array($value)) {
    //         $value = collection($value);
    //     }

    //     return $value;
    // }

    /**
     * バリデーションエラー一覧を取得します。
     * フォーマットが「フィールド => [定義 => メッセージ]」となっているため、メッセージのみ抽出
     *
     * @return array
     */
    public function getValidateErrors()
    {
        return array_values(collection($this->getErrors())->map(function ($error) {
            return array_shift($error);
        })->toArray());
    }

    /**
     * 所属國を取得します。
     *
     * @param mixed $value 値
     * @return Country
     */
    protected function _getCountry($value)
    {
        if ($value) {
            return $value;
        }

        $result = TableRegistry::get('Countries')->get($this->country_id);

        return $this->country = $result;
    }

    /**
     * 段位を取得します。
     *
     * @param mixed $value 値
     * @return Rank
     */
    protected function _getRank($value)
    {
        if ($value) {
            return $value;
        }

        $result = TableRegistry::get('Ranks')->get($this->rank_id);

        return $this->rank = $result;
    }
}
