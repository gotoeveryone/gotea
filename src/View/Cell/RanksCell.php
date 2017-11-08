<?php

namespace Gotea\View\Cell;

use Cake\View\Cell;

/**
 * 段位を表示するためのセル
 */
class RanksCell extends Cell
{
    /**
     * 表示処理
     *
     * @param bool $empty ブランクを設定するか
     * @param string $value デフォルトの選択値
     * @return void
     */
    public function display($empty = false, $value = '')
    {
        $ranks = $this->loadModel('Ranks');
        $this->set('empty', $empty)
            ->set('value', ($req = $this->request->getData('rank_id')) ? $req : $value)
            ->set('ranks', $ranks->findProfessional()->combine('id', 'name'));
    }
}
