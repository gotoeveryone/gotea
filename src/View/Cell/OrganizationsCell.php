<?php

namespace Gotea\View\Cell;

use Cake\View\Cell;

/**
 * 所属組織を表示するためのセル
 *
 * @property \Gotea\Model\Table\OrganizationsTable $Organizations
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
        $this->loadModel('Organizations');
        $organizations = $this->Organizations->findSorted();
        $this->set(compact('organizations', 'attributes'));
    }
}
