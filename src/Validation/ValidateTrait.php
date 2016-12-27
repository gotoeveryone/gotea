<?php
namespace App\Validation;

/**
 * バリデーションメッセージを出力します。
 *
 * @author      Kazuki Kamizuru
 * @since		2015/07/26
 */
trait ValidateTrait
{
    public $REQUIRED = 'required';
    public $NUMERIC = 'numeric';
    public $ALPHA_NUMERIC = 'alphaNumeric';
    public $LENGTH_BETWEEN = 'lengthBetween';
    public $MIN_LENGTH = 'minLength';
    public $MAX_LENGTH = 'maxLength';
    public $RANGE = 'range';
    public $INLALID_FORMAT = 'invalidFormat';

    /**
     * 必須入力のメッセージを取得します。
     * 
     * @param string $key
     * @param null|array $args
     * @return string|null Translated string.
     */
    public function getMessage(string $key, $args = null) {
        // see \App\Locale\{code}\default.po
        $messages = [
            'required' => 'field {0} is required',
            'numeric' => 'field {0} is numeric value only',
            'alphaNumeric' => 'field {0} is alpha or numeric value only',
            'lengthBetween' => 'field {0} length is {1} - {2}',
            'minLength' => 'field {0} length is over the {1}',
            'maxLength' => 'field {0} length is under the {1}',
            'range' => 'field {0} range is {1} - {2}',
            'invalidFormat' => 'field {0} is {1} format only',
        ];
        return __d('default', (array_key_exists($key, $messages) ? $messages[$key] : ''), $args);
    }
}
