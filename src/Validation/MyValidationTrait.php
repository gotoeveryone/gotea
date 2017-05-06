<?php
namespace App\Validation;

/**
 * バリデーションメッセージを出力します。
 *
 * @author      Kazuki Kamizuru
 * @since		2015/07/26
 */
trait MyValidationTrait
{
    public $REQUIRED = 'required';
    public $NUMERIC = 'numeric';
    public $ALPHA_NUMERIC = 'alphaNumeric';
    public $LENGTH_BETWEEN = 'lengthBetween';
    public $MIN_LENGTH = 'minLength';
    public $MAX_LENGTH = 'maxLength';
    public $RANGE = 'range';
    public $INLALID_FORMAT = 'invalidFormat';

    private $_messages = [
        'required' => 'field {0} is required',
        'numeric' => 'field {0} is numeric value only',
        'alphaNumeric' => 'field {0} is alpha or numeric value only',
        'lengthBetween' => 'field {0} length is {1} - {2}',
        'minLength' => 'field {0} length is over the {1}',
        'maxLength' => 'field {0} length is under the {1}',
        'range' => 'field {0} range is {1} - {2}',
        'invalidFormat' => 'field {0} is {1} format only',
    ];

    /**
     * {@inheritdoc}
     */
    public function alphaNumeric($check)
    {
        return (bool) preg_match('/^[a-zA-Z0-9\(\)\'\-\s]+$/', $check);
    }

    /**
     * 必須入力のメッセージを取得します。
     * 
     * @param string $key
     * @param null|array $args
     * @return string|null Translated string.
     */
    public function getMessage(string $key, $args = null) {
        // see \App\Locale\{code}\default.po
        return __d('default', ($this->_messages[$key] ?? ''), $args);
    }
}
