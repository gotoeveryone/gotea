<?php

namespace App\View\Cell;

use Cake\View\Cell;

class CountriesCell extends Cell
{
    /**
     * 所属国を表示するためのセル
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
