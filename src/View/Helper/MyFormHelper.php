<?php
namespace Gotea\View\Helper;

use Cake\View\Helper\FormHelper;
use Gotea\Utility\CalculatorTrait;

/**
 * 独自にカスタマイズしたFormヘルパー
 */
class MyFormHelper extends FormHelper
{
    use CalculatorTrait;

    /**
     * 性別一覧を取得します。
     *
     * @param array $attributes
     * @return string Formatted SELECT element
     */
    public function sexes(array $attributes = [])
    {
        return $this->select('sex', [
            '男性' => '男性',
            '女性' => '女性'
        ], $attributes);
    }

    /**
     * 管理年度一覧を取得します。
     *
     * @param string $name プロパティ名
     * @param array $attributes 属性
     * @return string Formatted SELECT element
     */
    public function years(string $name, array $attributes = [])
    {
        // 年度プルダウン
        $years = [];
        for ($i = date('Y'); $i >= 2013; $i--) {
            $years[$i] = $i.'年度';
        }
        return $this->select('sex', $years, $attributes);
    }
}
