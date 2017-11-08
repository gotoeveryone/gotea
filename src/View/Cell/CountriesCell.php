<?php

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
     * @param bool $hasTitle タイトル保持しているか
     * @param array $customOptions 追加属性
     * @return void
     */
    public function display($hasTitle = false, $customOptions = [])
    {
        $countries = $this->loadModel('Countries');
        $this->set('countries', $countries->findAllHasCode($hasTitle)->combine('id', 'name'))
            ->set('customOptions', $customOptions);
    }
}
