<?php
use Migrations\AbstractMigration;

class AddIsOfficialToRetentionHistories extends AbstractMigration
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
            ->addColumn('is_official', 'boolean', [
                'comment' => '公式戦フラグ',
                'default' => true,
                'limit' => null,
                'null' => false,
                'after' => 'acquired',
            ])
            ->update();
    }
}
