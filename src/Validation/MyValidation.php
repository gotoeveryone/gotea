<?php

namespace App\Validation;

use Cake\Validation\Validation;
use Gotoeveryone\Validation\CustomValidationTrait;

/**
 * バリデーションメッセージを出力します。
 *
 * @author      Kazuki Kamizuru
 * @since       2015/07/26
 */
class MyValidation extends Validation
{
    use CustomValidationTrait;
}
