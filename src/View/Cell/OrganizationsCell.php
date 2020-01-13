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
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        // CakePHP 4.x からはこのパスになるため、アップグレード時にこの記述は不要になる
        // https://book.cakephp.org/4/ja/appendices/4-0-migration-guide.html
        $this->viewBuilder()
            ->setTemplatePath('cell' . DS . str_replace('\\', DS, $name));
    }

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
