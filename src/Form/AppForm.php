<?php

namespace Gotea\Form;

use Cake\Form\Form;
use Cake\Validation\Validator;
use Gotea\Validation\MyValidator;

/**
 * 基底フォーム
 */
class AppForm extends Form
{
    /**
     * {@inheritDoc}
     */
    public function validator(Validator $validator = null)
    {
        if ($validator === null && empty($this->_validator)) {
            $validator = $this->_buildValidator(new MyValidator());
        }

        return parent::validator($validator);
    }
}
