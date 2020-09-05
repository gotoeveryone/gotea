<?php
declare(strict_types=1);

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
     * @inheritDoc
     */
    protected function _buildSchema(Schema $schema): Schema
    {
        return $schema->addField('account', [
            'type' => 'string', 'length' => 10,
        ])->addField('password', 'password');
    }

    /**
     * @inheritDoc
     */
    public function buildValidator(Event $event, Validator $validator, $name): void
    {
        // ユーザID
        $validator
            ->requirePresence('account')
            ->maxLength('account', 10)
            ->asciiAlphaNumeric('account');

        // パスワード
        $validator
            ->requirePresence('password')
            ->maxLength('password', 20)
            ->password('password');
    }
}
