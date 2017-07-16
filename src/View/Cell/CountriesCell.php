<?php

namespace App\View\Cell;

use Cake\View\Cell;

/**
 * 所属国を表示するためのセル
 */
class CountriesCell extends Cell
{
    /**
     * 表示処理
     *
     * @param boolean $hasTitle
     * @return void
     */
    public function display($hasTitle = false)
    {
        $countries = $this->loadModel('Countries');
        $this->set('countries', $countries->findToKeyValue($hasTitle));
    }
}
