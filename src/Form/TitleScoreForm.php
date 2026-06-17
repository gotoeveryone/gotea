<?php
declare(strict_types=1);

namespace Gotea\Form;

use Cake\Event\Event;
use Cake\Form\Schema;
use Cake\Validation\Validator;

/**
 * タイトル成績検索用フォーム
 */
class TitleScoreForm extends AppForm
{
    /**
     * @inheritDoc
     */
    protected function _buildSchema(Schema $schema): Schema
    {
        return $schema->addFields([
            'name' => ['type' => 'string', 'length' => 20],
            'title_name' => ['type' => 'string', 'length' => 20],
            'country_id' => 'integer',
            'target_year' => 'integer',
            'started' => 'string',
            'ended' => 'string',
        ]);
    }

    /**
     * @inheritDoc
     */
    public function buildValidator(Event $event, Validator $validator, $name): void
    {
        $validator
            ->allowEmptyString('name')
            ->allowEmptyString('title_name')
            ->allowEmptyString('country_id')
            ->allowEmptyString('target_year')
            ->allowEmptyDate('started')
            ->allowEmptyDate('ended')
            ->integer('country_id')
            ->range('target_year', [1, 9999])
            ->maxLength('name', 20)
            ->maxLength('title_name', 20)
            ->date('started', ['y-m-d'])
            ->date('ended', ['y-m-d']);
    }
}
