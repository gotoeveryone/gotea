<?php

namespace App\Form;

use Cake\Form\Schema;
use Cake\Validation\Validator;

/**
 * 棋士検索用フォーム
 */
class PlayerForm extends AppForm
{
    /**
     * {@inheritdoc}
     * 
     * @param \Cake\Form\Schema $schema
     * @return type
     */
    protected function _buildSchema(Schema $schema)
    {
        return $schema->addFields([
            'country_id' => 'string',
            'organization_id' => 'string',
            'rank_id' => 'string',
            'sex' => 'string',
            'is_retired' => 'string',
            'name' => ['type' => 'text'],
            'name_english' => ['type' => 'text'],
            'joined_from', ['type' => 'text'],
            'joined_to', ['type' => 'text']
        ]);
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
            ->allowEmpty(['name', 'name_english', 'joined_from', 'joined_to'])
            ->maxLength('name', 20, __d('default', 'field {0} length is under the {1}', ['棋士名', 20]))
            ->maxLength('name_english', 40, __d('default', 'field {0} length is under the {1}', ['棋士名（英語）', 40]))
            ->numeric('joined_from', '入段年（開始）は数値で入力してください。')
            ->range('joined_from', [1, 9999], __d('default', 'field {0} range is {1} - {2}', ['入段年（開始）', 1, 9999]))
            ->numeric('joined_to', '入段年（終了）は数値で入力してください。')
            ->range('joined_to', [1, 9999], __d('default', 'field {0} range is {1} - {2}', ['入段年（終了）', 1, 9999]));
    }
}
