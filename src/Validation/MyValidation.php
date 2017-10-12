<?php

namespace Gotea\Validation;

use Cake\Validation\Validation;
use Gotoeveryone\Validation\CustomValidationTrait;

/**
 * カスタムのバリデーションクラスです。
 */
class MyValidation extends Validation
{
    use CustomValidationTrait;
}
