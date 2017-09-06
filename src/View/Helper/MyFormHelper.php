<?php
namespace App\View\Helper;

use Cake\I18n\Number;
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
     * @param string 勝率（整数値）
     */
    public function percent(int $win, int $lose)
    {
        $sum = $win + $lose;
        $calc = $sum === 0 ? 0 : ($win / $sum);
        return Number::toPercentage($calc, 0, [
            'multiply' => true,
        ]);
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
