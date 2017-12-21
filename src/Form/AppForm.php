<?php

namespace Gotea\Form;

use Cake\Form\Form;
use Cake\Validation\Validator as BaseValidator;
use Gotea\Validation\Validator;

/**
 * 基底フォーム
 */
class AppForm extends Form
{
    /**
     * {@inheritDoc}
     */
    public function validator(BaseValidator $validator = null)
    {
        if ($validator === null && empty($this->_validator)) {
            $validator = $this->_buildValidator(new Validator());
        }

        return parent::validator($validator);
    }
}
