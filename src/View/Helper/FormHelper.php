<?php
declare(strict_types=1);

namespace Gotea\View\Helper;

use Cake\I18n\FrozenDate;
use Cake\View\View;
use Gotea\Utility\CalculatorTrait;
use Shim\View\Helper\FormHelper as BaseFormHelper;

/**
 * 独自にカスタマイズしたFormヘルパー
 */
class FormHelper extends BaseFormHelper
{
    use CalculatorTrait;

    /**
     * @inheritDoc
     */
    public function __construct(View $View, array $config = [])
    {
        parent::__construct($View, $config);

        $this->setTemplates([
            'nestingLabel' => '{{hidden}}<label{{attrs}}>{{input}}<span class="text">{{text}}</span></label>',
            'date' => '{{year}}{{month}}{{day}}',
            'datetime' => '{{year}}{{month}}{{day}}{{hour}}{{minute}}{{second}}{{meridian}}',
            // エラーメッセージは個別に出力しない
            'error' => '',
        ]);
        $this->addWidget('date', ['Gotea.DateTime', 'select']);
        $this->addWidget('datetime', ['Gotea.DateTime', 'select']);
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

    /**
     * 入段日を取得します。
     * TODO: 4.x ではこの機能が削除されたため現在は Shim プラグインを使って実現しているが、最終的には利用しないようにしたい
     *
     * @param string $name フィールド名
     * @param array $attributes オプション
     * @return string Generated set of select boxes for time formats chosen.
     */
    public function joined(string $name, array $attributes = [])
    {
        return $this->control($name, [
            'type' => 'date',
            'year' => [
                'start' => '1920',
                'end' => FrozenDate::now()->addYears(1)->year,
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
     * 日時をセレクトボックスで取得します。
     * TODO: 4.x ではこの機能が削除されたため現在は Shim プラグインを使って実現しているが、最終的には利用しないようにしたい
     *
     * @param string $name フィールド名
     * @param array $attributes オプション
     * @return string Generated set of select boxes for time formats chosen.
     */
    public function datetimeSelect(string $name, array $attributes = [])
    {
        return $this->control($name, [
            'type' => 'datetime',
            'year' => [
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
            'hour' => [
                'class' => 'input-row dropdowns',
                'suffix' => '時',
            ],
            'minute' => [
                'class' => 'input-row dropdowns',
                'suffix' => '分',
            ],
            'second' => [
                'class' => 'input-row dropdowns',
                'suffix' => '秒',
            ],
        ] + $attributes);
    }
}
