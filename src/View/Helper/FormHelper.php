<?php
declare(strict_types=1);

namespace Gotea\View\Helper;

use Cake\I18n\FrozenDate;
use Cake\View\Helper\FormHelper as BaseFormHelper;
use Cake\View\View;
use Gotea\Utility\CalculatorTrait;

/**
 * 独自にカスタマイズしたFormヘルパー
 */
class FormHelper extends BaseFormHelper
{
    use CalculatorTrait;

    /**
     * The various pickers that make up a datetime picker.
     *
     * @var array<string>
     */
    protected array $_datetimeParts = ['year', 'month', 'day', 'hour', 'minute', 'second', 'meridian'];

    /**
     * Special options used for datetime inputs.
     *
     * @var array<string>
     */
    protected array $_datetimeOptions = [
        'interval', 'round', 'monthNames', 'minYear', 'maxYear',
        'orderYear', 'timeFormat', 'second',
    ];

    /**
     * Grouped input types.
     *
     * @var array<string>
     */
    protected array $_groupedInputTypes = ['radio', 'multicheckbox', 'date', 'time', 'datetime'];

    /**
     * @inheritDoc
     */
    public function __construct(View $View, array $config = [])
    {
        parent::__construct($View, $config);

        $this->setTemplates([
            'nestingLabel' => '{{hidden}}<label{{attrs}}>{{input}}<span class="text">{{text}}</span></label>',
            // エラーメッセージは個別に出力しない
            'error' => '',
        ]);
    }

    /**
     * 性別一覧を取得します。
     *
     * @param array $attributes 追加属性
     * @return string Formatted SELECT element
     */
    public function sexes(array $attributes = []): string
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
    public function filters(string $name, array $attributes = []): string
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
    public function years(string $name, array $attributes = []): string
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
     * 入段年一覧を取得します。
     *
     * @param string $name プロパティ名
     * @param array $attributes 属性
     * @return string Formatted SELECT element
     */
    public function joinedYears(string $name = 'joined_year', array $attributes = []): string
    {
        $years = [];
        for ($i = FrozenDate::now()->addYears(1)->year; $i >= 1920; $i--) {
            $years[$i] = $i . '年';
        }

        return $this->select($name, $years, [
            'empty' => false,
        ] + $attributes);
    }

    /**
     * 入段月一覧を取得します。
     *
     * @param string $name プロパティ名
     * @param array $attributes 属性
     * @return string Formatted SELECT element
     */
    public function joinedMonths(string $name = 'joined_month', array $attributes = []): string
    {
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = $i . '月';
        }

        return $this->select($name, $months, [
            'empty' => ['' => '-'],
        ] + $attributes);
    }

    /**
     * 入段日一覧を取得します。
     *
     * @param string $name プロパティ名
     * @param array $attributes 属性
     * @return string Formatted SELECT element
     */
    public function joinedDays(string $name = 'joined_day', array $attributes = []): string
    {
        $days = [];
        for ($i = 1; $i <= 31; $i++) {
            $days[$i] = $i . '日';
        }

        return $this->select($name, $days, [
            'empty' => ['' => '-'],
        ] + $attributes);
    }
}
