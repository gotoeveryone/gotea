<?php

namespace App\Form;

use Cake\Form\Schema;
use Cake\Validation\Validator;

/**
 * ログイン用フォーム
 */
class LoginForm extends AppForm
{
    /**
     * {@inheritdoc}
     * 
     * @param \Cake\Form\Schema $schema
     * @return type
     */
    protected function _buildSchema(Schema $schema)
    {
        return $schema->addField('username', ['type' => 'string'])
            ->addField('password', ['type' => 'password']);
    }

    /**
     * {@inheritdoc}
     * 
     * @param Validator $validator
     * @return type
     */
    protected function _buildValidator(Validator $validator)
    {
        return $validator
            ->notEmpty('username', $this->getMessage($this->REQUIRED, 'ID'))
            ->maxLength('username', 10, $this->getMessage($this->MAX_LENGTH, ['ID', 10]))
            ->notEmpty('password', $this->getMessage($this->REQUIRED, 'パスワード'))
            ->maxLength('password', 20, $this->getMessage($this->MAX_LENGTH, ['パスワード', 20]));
    }
}
