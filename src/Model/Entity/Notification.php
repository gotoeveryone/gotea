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
 *
 * @property bool $is_published
 */
class Notification extends AppEntity
{
    /**
     * @inheritDoc
     */
    protected array $_accessible = [
        'title' => true,
        'content' => true,
        'is_draft' => true,
        'published' => true,
        'is_permanent' => true,
        'created' => true,
        'modified' => true,
    ];

    /**
     * @inheritDoc
     */
    protected array $_virtual = [
        'status',
    ];

    /**
     * 状態を取得します。
     *
     * @return string
     */
    protected function _getStatus(): string
    {
        return $this->is_draft ? __('下書き') : __('公開');
    }

    /**
     * 公開状態かを判定します。
     *
     * @return bool
     */
    protected function _getIsPublished(): bool
    {
        return !$this->is_draft;
    }
}
