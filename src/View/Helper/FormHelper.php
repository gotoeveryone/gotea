<?php
declare(strict_types=1);

namespace Gotea\View\Helper;

use DateTime;
use Cake\I18n\Date;
use Cake\View\View;
use Gotea\Utility\CalculatorTrait;
use Cake\View\Helper\FormHelper as BaseFormHelper;

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
	protected $_datetimeParts = ['year', 'month', 'day', 'hour', 'minute', 'second', 'meridian'];

	/**
	 * Special options used for datetime inputs.
	 *
	 * @var array<string>
	 */
	protected $_datetimeOptions = [
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
            'date' => '{{year}}{{month}}{{day}}',
            'datetime' => '{{year}}{{month}}{{day}}{{hour}}{{minute}}{{second}}{{meridian}}',
            'dateWidget' => '{{year}}{{month}}{{day}}{{hour}}{{minute}}{{second}}{{meridian}}',
            // エラーメッセージは個別に出力しない
            'error' => '',
        ]);
        $this->addWidget('date', ['Gotea.DateTime', 'select']);
        $this->addWidget('datetime', ['Gotea.DateTime', 'select']);
    }

	/**
	 * Helper method for the various single datetime component methods.
	 *
	 * @param array<string, mixed> $options The options array.
	 * @param string $keep The option to not disable.
	 * @return array<string, mixed>
	 */
	protected function _singleDatetime(array $options, string $keep): array {
		$offKeys = array_diff($this->_datetimeParts, [$keep]);
		$offValues = array_fill(0, count($offKeys), false);
		$off = (array)array_combine(
			$offKeys,
			$offValues,
		);

		$attributes = array_diff_key(
			$options,
			array_flip(array_merge($this->_datetimeOptions, ['value', 'empty'])),
		);

		/** @var array<string, mixed> $options */
		$options = $options + $off + [$keep => $attributes];
		if (isset($options['value'])) {
			$options['val'] = $options['value'];
		}

		return $options;
	}

	/**
	 * Returns a SELECT element for days.
	 *
	 * ### Options:
	 *
	 * - `empty` - If true, the empty select option is shown. If a string,
	 *   that string is displayed as the empty element.
	 * - `value` The selected value of the input.
	 *
	 * @link https://book.cakephp.org/3.0/en/views/helpers/form.html#creating-day-inputs
	 * @param string|null $fieldName Prefix name for the SELECT element
	 * @param array<string, mixed> $options Options & HTML attributes for the select element
	 * @return string A generated day select box.
	 */
	public function day(?string $fieldName = null, array $options = []): string {
		if ($fieldName === null) {
			// Deprecated?

			return '';
		}

		$options = $this->_singleDatetime($options, 'day');

		if (isset($options['val']) && $options['val'] > 0 && $options['val'] <= 31) {
			$options['val'] = [
				'year' => date('Y'),
				'month' => date('m'),
				'day' => (int)$options['val'],
			];
		}

		return $this->dateTime($fieldName, $options);
	}

	/**
	 * Returns a SELECT element for years
	 *
	 * ### Attributes:
	 *
	 * - `empty` - If true, the empty select option is shown. If a string,
	 *   that string is displayed as the empty element.
	 * - `orderYear` - Ordering of year values in select options.
	 *   Possible values 'asc', 'desc'. Default 'desc'
	 * - `value` The selected value of the input.
	 * - `maxYear` The max year to appear in the select element.
	 * - `minYear` The min year to appear in the select element.
	 *
	 * @link https://book.cakephp.org/3.0/en/views/helpers/form.html#creating-year-inputs
	 * @param string $fieldName Prefix name for the SELECT element
	 * @param array<string, mixed> $options Options & attributes for the select elements.
	 * @return string Completed year select input
	 */
	public function year(string $fieldName, array $options = []): string {
		$options = $this->_singleDatetime($options, 'year');

		$len = isset($options['val']) ? strlen($options['val']) : 0;
		if (isset($options['val']) && $len > 0 && $len < 5) {
			$options['val'] = [
				'year' => (int)$options['val'],
				'month' => date('m'),
				'day' => date('d'),
			];
		}

		return $this->dateTime($fieldName, $options);
	}

	/**
	 * Returns a SELECT element for months.
	 *
	 * ### Options:
	 *
	 * - `monthNames` - If false, 2 digit numbers will be used instead of text.
	 *   If an array, the given array will be used.
	 * - `empty` - If true, the empty select option is shown. If a string,
	 *   that string is displayed as the empty element.
	 * - `value` The selected value of the input.
	 *
	 * @link https://book.cakephp.org/3.0/en/views/helpers/form.html#creating-month-inputs
	 * @param string $fieldName Prefix name for the SELECT element
	 * @param array<string, mixed> $options Attributes for the select element
	 * @return string A generated month select dropdown.
	 */
	public function month(string $fieldName, array $options = []): string {
		$options = $this->_singleDatetime($options, 'month');

		if (isset($options['val']) && $options['val'] > 0 && $options['val'] <= 12) {
			$options['val'] = [
				'year' => date('Y'),
				'month' => (int)$options['val'],
				'day' => date('d'),
			];
		}

		return $this->dateTime($fieldName, $options);
	}

	/**
	 * Returns a SELECT element for hours.
	 *
	 * ### Attributes:
	 *
	 * - `empty` - If true, the empty select option is shown. If a string,
	 *   that string is displayed as the empty element.
	 * - `value` The selected value of the input.
	 * - `format` Set to 12 or 24 to use 12 or 24 hour formatting. Defaults to 24.
	 *
	 * @link https://book.cakephp.org/3.0/en/views/helpers/form.html#creating-hour-inputs
	 * @param string $fieldName Prefix name for the SELECT element
	 * @param array<string, mixed> $options List of HTML attributes
	 * @return string Completed hour select input
	 */
	public function hour(string $fieldName, array $options = []): string {
		$options += ['format' => 24];
		$options = $this->_singleDatetime($options, 'hour');

		$options['timeFormat'] = $options['format'];
		unset($options['format']);

		if (isset($options['val']) && $options['val'] > 0 && $options['val'] <= 24) {
			$options['val'] = [
				'hour' => (int)$options['val'],
				'minute' => date('i'),
			];
		}

		return $this->dateTime($fieldName, $options);
	}

	/**
	 * Returns a SELECT element for minutes.
	 *
	 * ### Attributes:
	 *
	 * - `empty` - If true, the empty select option is shown. If a string,
	 *   that string is displayed as the empty element.
	 * - `value` The selected value of the input.
	 * - `interval` The interval that minute options should be created at.
	 * - `round` How you want the value rounded when it does not fit neatly into an
	 *   interval. Accepts 'up', 'down', and null.
	 *
	 * @link https://book.cakephp.org/3.0/en/views/helpers/form.html#creating-minute-inputs
	 * @param string $fieldName Prefix name for the SELECT element
	 * @param array<string, mixed> $options Array of options.
	 * @return string Completed minute select input.
	 */
	public function minute(string $fieldName, array $options = []): string {
		$options = $this->_singleDatetime($options, 'minute');

		if (isset($options['val']) && $options['val'] > 0 && $options['val'] <= 60) {
			$options['val'] = [
				'hour' => date('H'),
				'minute' => (int)$options['val'],
			];
		}

		return $this->dateTime($fieldName, $options);
	}

	/**
	 * Returns a SELECT element for AM or PM.
	 *
	 * ### Attributes:
	 *
	 * - `empty` - If true, the empty select option is shown. If a string,
	 *   that string is displayed as the empty element.
	 * - `value` The selected value of the input.
	 *
	 * @link https://book.cakephp.org/3.0/en/views/helpers/form.html#creating-meridian-inputs
	 * @param string $fieldName Prefix name for the SELECT element
	 * @param array<string, mixed> $options Array of options
	 * @return string Completed meridian select input
	 */
	public function meridian(string $fieldName, array $options = []): string {
		$options = $this->_singleDatetime($options, 'meridian');

		if (isset($options['val'])) {
			$hour = date('H');
			$options['val'] = [
				'hour' => $hour,
				'minute' => (int)$options['val'],
				'meridian' => $hour > 11 ? 'pm' : 'am',
			];
		}

		return $this->dateTime($fieldName, $options);
	}

	/**
	 * Returns a set of SELECT elements for a full datetime setup: day, month and year, and then time.
	 *
	 * ### Date Options:
	 *
	 * - `empty` - If true, the empty select option is shown. If a string,
	 *   that string is displayed as the empty element.
	 * - `value`|`default` The default value to be used by the input. A value in ` $this->data`
	 *   matching the field name will override this value. If no default is provided `time()` will be used.
	 * - `monthNames` If false, 2 digit numbers will be used instead of text.
	 *   If an array, the given array will be used.
	 * - `minYear` The lowest year to use in the year select
	 * - `maxYear` The maximum year to use in the year select
	 * - `orderYear` - Order of year values in select options.
	 *   Possible values 'asc', 'desc'. Default 'desc'.
	 *
	 * ### Time options:
	 *
	 * - `empty` - If true, the empty select option is shown. If a string,
	 * - `value`|`default` The default value to be used by the input. A value in ` $this->data`
	 *   matching the field name will override this value. If no default is provided `time()` will be used.
	 * - `timeFormat` The time format to use, either 12 or 24.
	 * - `interval` The interval for the minutes select. Defaults to 1
	 * - `round` - Set to `up` or `down` if you want to force rounding in either direction. Defaults to null.
	 * - `second` Set to true to enable seconds drop down.
	 *
	 * To control the order of inputs, and any elements/content between the inputs you
	 * can override the `dateWidget` template. By default the `dateWidget` template is:
	 *
	 * `{{month}}{{day}}{{year}}{{hour}}{{minute}}{{second}}{{meridian}}`
	 *
	 * @link https://book.cakephp.org/3.0/en/views/helpers/form.html#creating-date-and-time-inputs
	 * @param string $fieldName Prefix name for the SELECT element
	 * @param array<string, mixed> $options Array of Options
	 * @return string Generated set of select boxes for the date and time formats chosen.
	 */
	public function dateTime(string $fieldName, array $options = []): string {
		$options += [
			'empty' => true,
			'value' => null,
			'interval' => 1,
			'round' => null,
			'monthNames' => true,
			'minYear' => null,
			'maxYear' => null,
			'orderYear' => 'desc',
			'timeFormat' => 24,
			'second' => false,
		];
		$options = $this->_initInputField($fieldName, $options);
		$options = $this->_datetimeOptions($options);

		return $this->widget('datetime', $options);
	}

	/**
	 * Helper method for converting from FormHelper options data to widget format.
	 *
	 * @param array<string, mixed> $options Options to convert.
	 * @return array<string, mixed> Converted options.
	 */
	protected function _datetimeOptions(array $options): array {
		foreach ($this->_datetimeParts as $type) {
			if (!array_key_exists($type, $options)) {
				$options[$type] = [];
			}
			if ($options[$type] === true) {
				$options[$type] = [];
			}

			// Pass empty options to each type.
			if (!empty($options['empty']) &&
				is_array($options[$type])
			) {
				$options[$type]['empty'] = $options['empty'];
			}

			// Move empty options into each type array.
			if (isset($options['empty'][$type])) {
				$options[$type]['empty'] = $options['empty'][$type];
			}
			if (isset($options['required']) && is_array($options[$type])) {
				$options[$type]['required'] = $options['required'];
			}
		}

		$hasYear = is_array($options['year']);
		if ($hasYear && isset($options['minYear'])) {
			$options['year']['start'] = $options['minYear'];
		}
		if ($hasYear && isset($options['maxYear'])) {
			$options['year']['end'] = $options['maxYear'];
		}
		if ($hasYear && isset($options['orderYear'])) {
			$options['year']['order'] = $options['orderYear'];
		}
		unset($options['minYear'], $options['maxYear'], $options['orderYear']);

		if (is_array($options['month'])) {
			$options['month']['names'] = $options['monthNames'];
		}
		unset($options['monthNames']);

		if (is_array($options['hour']) && isset($options['timeFormat'])) {
			$options['hour']['format'] = $options['timeFormat'];
		}
		unset($options['timeFormat']);

		if (is_array($options['minute'])) {
			$options['minute']['interval'] = $options['interval'];
			$options['minute']['round'] = $options['round'];
		}
		unset($options['interval'], $options['round']);

		if ($options['val'] === true || $options['val'] === null
			&& isset($options['empty'])
			&& $options['empty'] === false
		) {
			$val = new DateTime();
			$currentYear = $val->format('Y');
			if (isset($options['year']['end']) && $options['year']['end'] < $currentYear) {
				$val->setDate((int)$options['year']['end'], (int)$val->format('n'), (int)$val->format('j'));
			}
			$options['val'] = $val;
		}

		unset($options['empty']);

		return $options;
	}

	/**
	 * Generate time inputs.
	 *
	 * ### Options:
	 *
	 * See dateTime() for time options.
	 *
	 * @see \Cake\View\Helper\FormHelper::dateTime() for templating options.
	 * @param string $fieldName Prefix name for the SELECT element
	 * @param array<string, mixed> $options Array of Options
	 * @return string Generated set of select boxes for time formats chosen.
	 */
	public function time(string $fieldName, array $options = []): string {
		$options += [
			'empty' => true,
			'value' => null,
			'interval' => 1,
			'round' => null,
			'timeFormat' => 24,
			'second' => false,
		];
		$options['year'] = $options['month'] = $options['day'] = false;
		$options = $this->_initInputField($fieldName, $options);
		$options = $this->_datetimeOptions($options);

		return $this->widget('datetime', $options);
	}

	/**
	 * Generate date inputs.
	 *
	 * ### Options:
	 *
	 * See dateTime() for date options.
	 *
	 * @see \Cake\View\Helper\FormHelper::dateTime() for templating options.
	 * @param string $fieldName Prefix name for the SELECT element
	 * @param array<string, mixed> $options Array of Options
	 * @return string Generated set of select boxes for time formats chosen.
	 */
	public function date(string $fieldName, array $options = []): string {
		$options += [
			'empty' => true,
			'value' => null,
			'monthNames' => true,
			'minYear' => null,
			'maxYear' => null,
			'orderYear' => 'desc',
		];
		$options['hour'] = $options['minute'] = false;
		$options['meridian'] = $options['second'] = false;

		$options = $this->_initInputField($fieldName, $options);
		$options = $this->_datetimeOptions($options);

		return $this->widget('datetime', $options);
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
     * 入段日を取得します。
     * TODO: 4.x ではこの機能が削除されたため現在は Shim プラグインを使って実現しているが、最終的には利用しないようにしたい
     *
     * @param string $name フィールド名
     * @param array $attributes オプション
     * @return string Generated set of select boxes for time formats chosen.
     */
    public function joined(string $name, array $attributes = []): string
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
     * 日時をセレクトボックスで取得します。
     * TODO: 4.x ではこの機能が削除されたため現在は Shim プラグインを使って実現しているが、最終的には利用しないようにしたい
     *
     * @param string $name フィールド名
     * @param array $attributes オプション
     * @return string Generated set of select boxes for time formats chosen.
     */
    public function datetimeSelect(string $name, array $attributes = []): string
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
