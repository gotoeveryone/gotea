<?php
use Cake\ORM\TableRegistry;
use Migrations\AbstractMigration;

class AddCountryIdToRetentionHistories extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $this->table('retention_histories')
            ->addColumn('country_id', 'integer', [
                'comment' => '優勝棋士出場国ID',
                'default' => null,
                'limit' => 11,
                'null' => true,
                'after' => 'player_id',
            ])
            ->addForeignKey(
                'country_id',
                'countries',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        // 既存データの更新
        $this->updateData();
    }

    private function updateData()
    {
        $table = TableRegistry::getTableLocator()->get('RetentionHistories');
        $table->updateAll([
            'country_id = (select country_id from players where player_id = players.id)',
        ], [
            'is_team = 0 and country_id is null',
        ]);
    }
}
