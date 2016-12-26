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
            ->notEmpty('username', 'IDは必須です。')
            ->notEmpty('password', 'Passwordは必須です。')
            ->add('username', [
                'length' => [
                    'rule' => ['maxLength', 10],
                    'message' => 'IDは10文字以下で入力してください。'
                ]
            ])
            ->add('password', [
                'length' => [
                    'rule' => ['maxLength', 20],
                    'message' => 'Passwordは20文字以下で入力してください。'
                ]
            ]);
    }
}
