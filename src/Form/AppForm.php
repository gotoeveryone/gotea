<?php

namespace App\Form;

use App\Validation\MyValidationTrait;
use Cake\Form\Form;

/**
 * 基底フォーム
 */
class AppForm extends Form
{
    use MyValidationTrait;
}
