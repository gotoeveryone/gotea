<?php
declare(strict_types=1);

namespace Gotea\Form;

use Cake\Event\Event;
use Cake\Form\Schema;
use Cake\Validation\Validator;

/**
 * 棋士検索用フォーム
 */
class PlayerForm extends AppForm
{
    /**
     * @inheritDoc
     */
    protected function _buildSchema(Schema $schema): Schema
    {
        return $schema->addFields([
            'country_id' => 'integer',
            'organization_id' => 'integer',
            'rank_id' => 'integer',
            'sex' => 'string',
            'is_retired' => 'boolean',
            'name' => ['type' => 'string', 'length' => 20],
            'name_english' => ['type' => 'string', 'length' => 40],
            'name_other' => ['type' => 'string', 'length' => 20],
            'joined_from' => 'integer',
            'joined_to' => 'integer',
        ]);
    }

    /**
     * @inheritDoc
     */
    public function buildValidator(Event $event, Validator $validator, $name): void
    {
        $validator
            ->allowEmptyString('country_id')
            ->allowEmptyString('organization_id')
            ->allowEmptyString('rank_id')
            ->allowEmptyString('is_retired')
            ->allowEmptyString('sex')
            ->allowEmptyString('name')
            ->allowEmptyString('name_english')
            ->allowEmptyString('name_other')
            ->allowEmptyString('joined_from')
            ->allowEmptyString('joined_to')
            ->integer('country_id')
            ->integer('rank_id')
            ->integer('organization_id')
            ->integer('is_retired')
            ->maxLength('name', 20)
            ->maxLength('name_english', 40)
            ->maxLength('name_other', 20)
            ->range('joined_from', [1, 9999])
            ->range('joined_to', [1, 9999])
            ->nameEnglish('name_english');
    }
}
