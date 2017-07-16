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

    /**
     * 性別一覧を取得します。
     *
     * @return string Formatted SELECT element
     */
    public function sexes(array $attributes = [])
    {
        return $this->select('sex', [
            '男性' => '男性',
            '女性' => '女性'
        ], $attributes);
    }
}
