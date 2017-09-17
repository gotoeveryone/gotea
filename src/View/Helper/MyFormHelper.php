<?php
namespace App\View\Helper;

use Cake\View\Helper\FormHelper;
use App\Utility\CalculatorTrait;

/**
 * 独自にカスタマイズしたFormヘルパー
 */
class MyFormHelper extends FormHelper
{
    use CalculatorTrait;

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
