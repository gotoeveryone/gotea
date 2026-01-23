<?php
declare(strict_types=1);

namespace Gotea\Validation;

use Cake\Validation\ValidationRule;
use Cake\Validation\Validator as BaseValidator;

/**
 * カスタムバリデータクラス
 */
class Validator extends BaseValidator
{
    public const DEFAULT_MESSAGE = 'field {0} is invalid';

    /**
     * メッセージ
     *
     * @var array
     */
    private array $_messages = [
        'required' => 'field {0} is required',
        'notEmpty' => 'field {0} cannot be left empty',
        'numeric' => 'field {0} is numeric value only',
        'integer' => 'field {0} is integer value only',
        'alphaNumeric' => 'field {0} is alpha or numeric value only',
        'lengthBetween' => 'field {0} length is {1} - {2}',
        'minLength' => 'field {0} length is over the {1}',
        'maxLength' => 'field {0} length is under the {1}',
        'range' => 'field {0} range is {1} - {2}',
        'invalidFormat' => 'field {0} is {1} format only',
        'naturalNumber' => 'field {0} is natural number only',
    ];

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();

        $this->setProvider('default', Validation::class);
    }

    /**
     * @inheritDoc
     */
    public function add(string $field, array|string $name, ValidationRule|array $rule = [])
    {
        // メッセージが存在しなければ、ルールに該当するメッセージを定義から取得
        if (empty($rule['message'])) {
            $args = [__d('model', $field)];
            if (isset($rule['rule']) && is_array($rule['rule'])) {
                $rules = $rule['rule'];
                array_shift($rules);
                $args = array_merge($args, $rules);
            }
            $rule['message'] = $this->getMessage((is_array($name) ? $name[0] : $name), $args);
        }

        return parent::add($field, $name, $rule);
    }

    /**
     * @inheritDoc
     */
    protected function _convertValidatorToArray($fieldName, $defaults = [], $settings = []): array
    {
        $results = parent::_convertValidatorToArray($fieldName, $defaults, $settings);
        foreach ($results as $name => $property) {
            // すでにメッセージがあれば何もしない
            if (!empty($property['message'])) {
                continue;
            }

            if (isset($property['mode'])) {
                // $propertyに`mode`があるのは`requirePresence()`利用時
                $results[$name]['message'] = $this->getMessage('required', [__d('model', $name)]);
            } elseif (isset($property['when'])) {
                // $propertyに`when`があるのは`allowEmpty()`および`notEmpty()`利用時
                $results[$name]['message'] = $this->getMessage('notEmpty', [__d('model', $name)]);
            }
        }

        return $results;
    }

    /**
     * @inheritDoc
     */
    public function date($field, $formats = ['ymd'], $message = null, $when = null)
    {
        if (!is_array($formats)) {
            $formats = [$formats];
        }

        // 日付フォーマットをメッセージに渡す
        $args = [
            __d('model', $field),
            implode(', ', $formats),
        ];
        $message = $this->getMessage('invalidFormat', $args);

        // フォーマットから「y」「M」「m」「d」以外を消し去る
        foreach ($formats as $key => $value) {
            $formats[$key] = preg_replace('/([^d|^m|^y|^M])/', '', $value);
        }

        return parent::date($field, $formats, $message, $when);
    }

    /**
     * パスワードが設定可能な文字のみで構成されているか
     *
     * @param string $field The field you want to apply the rule to.
     * @param string|null $message The error message when the rule fails.
     * @param callable|string|null $when Either 'create' or 'update' or a callable that returns
     *   true when the validation rule should be applied.
     * @see \Gotea\Validation\Validation::nameEnglish()
     * @return $this
     */
    public function password(string $field, ?string $message = null, string|callable|null $when = null)
    {
        $extra = array_filter(['on' => $when, 'message' => $message]);

        return $this->add($field, 'password', $extra + [
            'rule' => ['password'],
        ]);
    }

    /**
     * 英語名として利用可能な文字のみで構成されているか
     *
     * @param string $field The field you want to apply the rule to.
     * @param string|null $message The error message when the rule fails.
     * @param callable|string|null $when Either 'create' or 'update' or a callable that returns
     *   true when the validation rule should be applied.
     * @see \Gotea\Validation\Validation::nameEnglish()
     * @return $this
     */
    public function nameEnglish(string $field, ?string $message = null, string|callable|null $when = null)
    {
        $extra = array_filter(['on' => $when, 'message' => $message]);

        return $this->add($field, 'nameEnglish', $extra + [
            'rule' => ['nameEnglish'],
        ]);
    }

    /**
     * メッセージのキーを取得します。
     *
     * @param string $key メッセージのキー
     * @param array|null $args メッセージに設定する値
     * @return string|null Translated string.
     */
    public function getMessage(string $key, ?array ...$args): ?string
    {
        $message = $this->_messages[$key] ?? null;
        if ($message === null) {
            if (isset($args[0][0])) {
                return __d('validation', self::DEFAULT_MESSAGE, $args[0][0]);
            }

            return __d('validation', self::DEFAULT_MESSAGE);
        }

        // see \Gotea\Locale\{code}\validation.po
        return __d('validation', $message, ...$args);
    }
}
