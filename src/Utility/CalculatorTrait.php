<?php

namespace Gotea\Utility;

use Cake\I18n\Number;

/**
 * 計算処理
 */
trait CalculatorTrait
{
    /**
     * 勝率を計算します。
     *
     * @param int $win 勝数
     * @param int $lose 敗数
     * @return string 勝率（整数値）
     */
    public function percent(int $win, int $lose)
    {
        $sum = $win + $lose;
        $calc = $sum === 0 ? 0 : ($win / $sum);

        return Number::toPercentage($calc, 0, [
            'multiply' => true,
        ]);
    }
}
