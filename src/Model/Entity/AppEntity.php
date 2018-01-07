<?php

namespace Gotea\Model\Entity;

use Cake\ORM\Entity;

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
}
