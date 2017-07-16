<?php

namespace App\View\Cell;

use Cake\View\Cell;

class RanksCell extends Cell
{
    /**
     * 段位を表示するためのセル
     *
     * @param bool $empty
     * @param string $value
     * @return void
     */
    public function display($empty = false, $value = '')
    {
        $ranks = $this->loadModel('Ranks');
        $this->set('empty', $empty)
            ->set('value', ($value ? $value : $this->request->getData('rank_id')))
            ->set('ranks', $ranks->findToKeyValue());
    }
}
