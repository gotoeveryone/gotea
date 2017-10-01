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
        return $schema->addField('account', ['type' => 'string'])
            ->addField('password', ['type' => 'password']);
    }

    /**
     * {@inheritdoc}
     */
    protected function _buildValidator(Validator $validator)
    {
        // ユーザID
        $validator
            ->notEmpty('account')
            ->maxLength('account', 10)
            ->alphaNumeric('account');

        // パスワード
        $validator
            ->notEmpty('password')
            ->maxLength('password', 20)
            ->alphaNumeric('password');

        return $validator;
    }
}
