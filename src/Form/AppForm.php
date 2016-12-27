<?php

namespace App\Form;

use App\Validation\ValidateTrait;
use Cake\Form\Form;

/**
 * 基底フォーム
 */
class AppForm extends Form
{
    use ValidateTrait;
}
