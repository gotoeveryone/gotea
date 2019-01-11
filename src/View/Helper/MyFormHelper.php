<?php
namespace Gotea\View\Helper;

use Cake\I18n\Date;
use Cake\View\Helper\FormHelper;
use Cake\View\View;
use Gotea\Utility\CalculatorTrait;

/**
 * 独自にカスタマイズしたFormヘルパー
 */
class MyFormHelper extends FormHelper
{
    use CalculatorTrait;

    /**
     * {@inheritDoc}
     */
    public function __construct(View $View, array $config = [])
    {
        // エラーメッセージは個別に出力しない
        $this->setTemplates([
            // Error message wrapper elements.
            'error' => false,
        ]);
        parent::__construct($View, $config);
    }

    /**
     * 入段日を取得します。
     *
     * @param string $name フィールド名
     * @param array $attributes オプション
     * @return string Generated set of select boxes for time formats chosen.
     */
    public function selectDate(string $name, array $attributes)
    {
        return $this->control($name, [
            'type' => 'date',
            'year' => [
                'start' => '1920',
                'end' => Date::now()->addYears(1)->year,
                'class' => 'input-row dropdowns',
                'suffix' => '年',
            ],
            'month' => [
                'class' => 'input-row dropdowns',
                'suffix' => '月',
            ],
            'day' => [
                'class' => 'input-row dropdowns',
                'suffix' => '日',
            ],
            'monthNames' => false,
        ] + $attributes);
    }

    /**
     * 性別一覧を取得します。
     *
     * @param array $attributes 追加属性
     * @return string Formatted SELECT element
     */
    public function sexes(array $attributes = [])
    {
        return $this->control('sex', [
            'type' => 'select',
            'options' => [
                '男性' => '男性',
                '女性' => '女性',
            ],
        ] + $attributes);
    }

    /**
     * 検索フィルタを取得します。
     *
     * @param string $name 名前
     * @param array $attributes 追加属性
     * @return string Formatted SELECT element
     */
    public function filters(string $name, array $attributes = [])
    {
        return $this->control($name, [
            'type' => 'select',
            'options' => [
                '0' => '検索しない',
                '1' => '検索する',
            ],
        ] + $attributes);
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
            $years[$i] = $i . '年度';
        }

        return $this->control($name, [
            'options' => $years,
        ] + $attributes);
    }
}
