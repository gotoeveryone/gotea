<?php
use Cake\ORM\TableRegistry;
use Migrations\AbstractMigration;

class AddAcquiredAtToRetentionHistories extends AbstractMigration
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
        $table = $this->table('retention_histories');
        $table->addColumn('acquired', 'date', [
            'comment' => '取得日',
            'default' => null,
            'limit' => null,
            'null' => false,
            'after' => 'is_team',
        ]);
        $table->update();

        // 既存データの更新
        $this->updateData();
    }

    private function updateData()
    {
        $table = TableRegistry::getTableLocator()->get('RetentionHistories');
        $table->updateAll([
            'acquired = created',
        ], [
            'acquired is null',
        ]);
    }
}
