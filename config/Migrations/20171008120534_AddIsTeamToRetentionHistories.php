<?php
use Migrations\AbstractMigration;

class AddIsTeamToRetentionHistories extends AbstractMigration
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
        $table->addColumn('is_team', 'boolean', [
            'comment' => '団体戦判定',
            'default' => false,
            'limit' => null,
            'null' => false,
            'after' => 'win_group_name',
        ]);
        $table->update();
    }
}
