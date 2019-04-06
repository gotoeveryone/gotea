<?php

namespace Gotea\View\Cell;

use Cake\View\Cell;

/**
 * 所属国を表示するためのセル
 *
 * @property \Gotea\Model\Table\CountriesTable $Countries
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
        $this->loadModel('Countries');
        $countries = $this->Countries->findAllHasCode($hasTitleOnly)
            ->combine('id', 'name');
        $this->set(compact('countries', 'attributes'));
    }
}
