<?php
declare(strict_types=1);

namespace Gotea\View\Cell;

use Cake\View\Cell;

/**
 * 所属組織を表示するためのセル
 */
class OrganizationsCell extends Cell
{
    /**
     * 表示処理
     *
     * @param array $attributes 属性
     * @return void
     */
    public function display($attributes = [])
    {
        /** @var \Gotea\Model\Table\OrganizationsTable $table */
        $table = $this->fetchTable('Organizations');
        $organizations = $table->findSorted();
        $this->set(compact('organizations', 'attributes'));
    }
}
