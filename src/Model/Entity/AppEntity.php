<?php
declare(strict_types=1);

namespace Gotea\Model\Entity;

use Cake\ORM\Entity;

/**
 * アプリケーションの共通エンティティ
 */
class AppEntity extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    // phpcs:ignore
    protected $_accessible = [
        '*' => true,
        'id' => false,
        'created' => false,
        'modified' => false,
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    // phpcs:ignore
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
    public function getValidateErrors(): array
    {
        return array_values(collection($this->getErrors())->map(function ($error) {
            return array_shift($error);
        })->toArray());
    }
}
