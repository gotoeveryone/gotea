<?php

namespace App\Validation;

use Cake\Validation\Validator;

/**
 * Undocumented class
 */
class MyValidator extends Validator
{
    /**
     * メッセージ
     *
     * @var array
     */
    private $_messages = [
        'required' => 'field {0} is required',
        'notEmpty' =>'field {0} cannot be left empty',
        'numeric' => 'field {0} is numeric value only',
        'alphaNumeric' => 'field {0} is alpha or numeric value only',
        'lengthBetween' => 'field {0} length is {1} - {2}',
        'minLength' => 'field {0} length is over the {1}',
        'maxLength' => 'field {0} length is under the {1}',
        'range' => 'field {0} range is {1} - {2}',
        'invalidFormat' => 'field {0} is {1} format only',
    ];

    /**
     * {@inheritDoc}
     */
    public function add($field, $name, $rule = [])
    {
        // メッセージが存在しなければデフォルトのメッセージを取得
        if (!isset($rule['message']) || $rule['message'] === '') {
            $key = $name;
            $args = [__d('validation', $field)];
            if (isset($rule['rule']) && is_array($rule['rule'])) {
                $rules = $rule['rule'];
                array_shift($rules);
                $args = array_merge($args, $rules);
            }
            $rule['message'] = $this->getMessage($key, $args);
        }
        return parent::add($field, $name, $rule);
    }

    /**
     * {@inheritDoc}
     */
    protected function _convertValidatorToArray($fieldName, $defaults = [], $settings = [])
    {
        $results = parent::_convertValidatorToArray($fieldName, $defaults, $settings);
        foreach ($results as $name => &$property) {
            if (isset($property['mode'])) {
                // プロパティが存在しない
                $property['message'] = $this->getMessage('required', __d('validation', $name));
            } else if (isset($property['when'])) {
                // プロパティが空欄
                $property['message'] = $this->getMessage('notEmpty', __d('validation', $name));
            }
        }
        return $results;
    }

    /**
     * {@inheritDoc}
     */
    public function date($field, $formats = ['ymd'], $message = null, $when = null)
    {
        $args = [__d('validation', $field)];
        if (!is_array($formats)) {
            $formats = [$formats];
        }
        $args[] = implode('/', str_split($formats[0]));
        $message = $this->getMessage('invalidFormat', $args);
        return parent::date($field, $formats, $message, $when);
    }

    /**
     * メッセージのキーを取得します。
     *
     * @param string $key
     * @param null|array $args
     * @return string|null Translated string.
     */
    public function getMessage(string $key, ...$args)
    {
        $message = $this->_messages[$key] ?? null;
        if (!$message) {
            return null;
        }

        // see \App\Locale\{code}\validation.po
        return __d('validation', $message, ...$args);
    }
}
