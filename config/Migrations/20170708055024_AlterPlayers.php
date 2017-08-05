<?php
use Migrations\AbstractMigration;

class AlterPlayers extends AbstractMigration
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
        $table = $this->table('players');
        $table->addColumn('retired', 'date', [
            'comment' => '引退日',
            'default' => null,
            'null' => true,
            'after' => 'is_retired',
        ]);
        $table->update();
    }
}
