<?php

namespace App\Form;

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
            'name' => ['type' => 'text'],
            'country_id' => 'string',
            'target_year' => 'string',
            'started', ['type' => 'text'],
            'ended', ['type' => 'text']
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
            ->maxLength('name', 20, $this->getMessage($this->MAX_LENGTH, ['棋士名', 20]))
            ->date('started', 'ymd', $this->getMessage($this->INLALID_FORMAT, ['対局日(FROM)', 'yyyy/MM/dd']))
            ->date('ended', 'ymd', $this->getMessage($this->INLALID_FORMAT, ['対局日(TO)', 'yyyy/MM/dd']));
    }
}
