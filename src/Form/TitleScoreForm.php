<?php

namespace Gotea\Form;

use Cake\Form\Schema;
use Cake\Validation\Validator;

/**
 * タイトル成績検索用フォーム
 */
class TitleScoreForm extends AppForm
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
            'name' => ['type' => 'string', 'length' => 20],
            'country_id' => 'integer',
            'target_year' => 'integer',
            'started' => 'string',
            'ended' => 'string',
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
            ->allowEmpty(['name', 'country_id', 'target_year', 'started', 'ended'])
            ->integer('country_id')
            ->range('target_year', [1, 9999])
            ->maxLength('name', 20)
            ->date('started', 'y/m/d')
            ->date('ended', 'y/m/d');
    }
}
