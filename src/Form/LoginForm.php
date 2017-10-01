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
     */
    protected function _buildSchema(Schema $schema)
    {
        return $schema->addField('username', ['type' => 'string'])
            ->addField('password', ['type' => 'password']);
    }

    /**
     * {@inheritdoc}
     */
    protected function _buildValidator(Validator $validator)
    {
        // ユーザID
        $validator
            ->notEmpty('username', $this->getMessage($this->REQUIRED, 'ID'))
            ->maxLength('username', 10, $this->getMessage($this->MAX_LENGTH, ['ID', 10]))
            ->alphaNumeric('username', $this->getMessage($this->ALPHA_NUMERIC, 'ID'));

        // パスワード
        $validator
            ->notEmpty('password', $this->getMessage($this->REQUIRED, 'パスワード'))
            ->maxLength('password', 20, $this->getMessage($this->MAX_LENGTH, ['パスワード', 20]))
            ->alphaNumeric('password', $this->getMessage($this->ALPHA_NUMERIC, 'パスワード'));

        return $validator;
    }
}
