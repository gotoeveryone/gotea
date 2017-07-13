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
            'name_other' => ['type' => 'text'],
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
            ->allowEmpty(['name', 'name_english', 'name_other', 'joined_from', 'joined_to'])
            ->maxLength('name', 20, $this->getMessage($this->MAX_LENGTH, ['棋士名', 20]))
            ->maxLength('name_english', 40, $this->getMessage($this->MAX_LENGTH, ['棋士名（英語）', 40]))
            ->add('name_english', 'default', [
                'rule' => [$this, 'alphaNumeric'],
                'message' => $this->getMessage($this->ALPHA_NUMERIC, '棋士名（英語）')
            ])
            ->maxLength('name_other', 20, $this->getMessage($this->MAX_LENGTH, ['棋士名（その他）', 20]))
            ->numeric('joined_from', $this->getMessage($this->NUMERIC, '入段年（開始）'))
            ->range('joined_from', [1, 9999], $this->getMessage($this->RANGE, ['入段年（開始）', 1, 9999]))
            ->numeric('joined_to', $this->getMessage($this->NUMERIC, '入段年（終了）'))
            ->range('joined_to', [1, 9999], $this->getMessage($this->RANGE, ['入段年（終了）', 1, 9999]));
    }
}
