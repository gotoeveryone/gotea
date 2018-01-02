<?php

namespace Gotea\Form;

use Cake\Form\Schema;
use Cake\Validation\Validator;

/**
 * 棋士検索用フォーム
 */
class PlayerForm extends AppForm
{
    /**
     * {@inheritdoc}
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
            'name_other' => ['type' => 'text'],
            'joined_from', ['type' => 'text'],
            'joined_to', ['type' => 'text']
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function _buildValidator(Validator $validator)
    {
        return $validator
            ->allowEmpty([
                'country_id', 'organization_id', 'rank_id', 'is_retired', 'sex',
                'name', 'name_english', 'name_other', 'joined_from', 'joined_to',
            ])
            ->numeric('country_id')
            ->numeric('rank_id')
            ->numeric('organization_id')
            ->numeric('is_retired')
            ->maxLength('name', 20)
            ->maxLength('name_english', 40)
            ->alphaNumeric('name_english')
            ->maxLength('name_other', 20)
            ->numeric('joined_from')
            ->range('joined_from', [1, 9999])
            ->numeric('joined_to')
            ->range('joined_to', [1, 9999]);
    }
}
