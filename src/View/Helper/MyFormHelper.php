<?php
namespace App\View\Helper;

use Cake\View\Helper\FormHelper;

/**
 * 独自にカスタマイズしたFormヘルパー
 */
class MyFormHelper extends FormHelper
{
    /**
     * 勝率を計算します。
     * 
     * @param int $win
     * @param int $lose
     * @param int 勝率（整数値）
     */
    public function percent(int $win, int $lose)
    {
        $sum = $win + $lose;
        if (!$sum) {
            return 0;
        }
        return round($win / ($sum) * 100);
    }
}
