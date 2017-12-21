<?php

namespace Gotea\Validation;

use Cake\Validation\Validation as BaseValidation;
use Gotoeveryone\Validation\CustomValidationTrait;

/**
 * カスタムのバリデーションクラスです。
 */
class Validation extends BaseValidation
{
    use CustomValidationTrait;
}
