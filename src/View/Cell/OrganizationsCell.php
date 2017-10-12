<?php

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
     * @param bool $empty
     * @param string $value
     * @return void
     */
    public function display($empty = false, $value = '')
    {
        $organizations = $this->loadModel('Organizations');
        $this->set('empty', $empty)
            ->set('value', ($req = $this->request->getData('organization_id')) ? $req : $value)
            ->set('organizations', $organizations->findSorted());
    }
}
