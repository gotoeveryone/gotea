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
            'name' => 'string',
            'name_english' => 'string',
            'name_other' => 'string',
            'joined_from', 'integer',
            'joined_to', 'integer'
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
            ->integer('country_id')
            ->integer('rank_id')
            ->integer('organization_id')
            ->integer('is_retired')
            ->alphaNumeric('name_english')
            ->maxLength('name', 20)
            ->maxLength('name_english', 40)
            ->maxLength('name_other', 20)
            ->range('joined_from', [1, 9999])
            ->range('joined_to', [1, 9999]);
    }
}
