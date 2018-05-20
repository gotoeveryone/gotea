<?php

namespace Gotea\Form;

use Cake\Event\Event;
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
        return $schema->addField('account', [
            'type' => 'string', 'length' => 10,
        ])->addField('password', 'password');
    }

    /**
     * {@inheritdoc}
     */
    public function buildValidator(Event $event, Validator $validator, $name)
    {
        // ユーザID
        $validator
            ->requirePresence('account')
            ->maxLength('account', 10)
            ->alphaNumeric('account');

        // パスワード
        $validator
            ->requirePresence('password')
            ->maxLength('password', 20)
            ->alphaNumeric('password');
    }
}
