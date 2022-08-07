<?php
declare(strict_types=1);

namespace Gotea\View\Cell;

use Cake\View\Cell;

/**
 * 所属国を表示するためのセル
 */
class CountriesCell extends Cell
{
    /**
     * 表示処理
     *
     * @param bool $hasTitleOnly タイトルを保持している国のみ表示するか
     * @param array $attributes 属性
     * @return void
     */
    public function display($hasTitleOnly = true, $attributes = [])
    {
        /** @var \Gotea\Model\Table\CountriesTable $table */
        $table = $this->fetchTable('Countries');
        $countries = $table->findAllHasCode($hasTitleOnly)
            ->all()
            ->combine('id', 'name');
        $this->set(compact('countries', 'attributes'));
    }
}
