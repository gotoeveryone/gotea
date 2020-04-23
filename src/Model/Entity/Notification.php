<?php
declare(strict_types=1);

namespace Gotea\Model\Entity;

/**
 * Notification Entity
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property bool $is_draft
 * @property string $status
 * @property bool $is_permanent
 * @property \Cake\I18n\FrozenTime $published
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 */
class Notification extends AppEntity
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
    protected $_accessible = [
        'title' => true,
        'content' => true,
        'is_draft' => true,
        'published' => true,
        'is_permanent' => true,
        'created' => true,
        'modified' => true,
    ];

    /**
     * 状態を取得します。
     *
     * @return string
     */
    protected function _getStatus()
    {
        return $this->is_draft ? __('下書き') : __('公開');
    }
}
