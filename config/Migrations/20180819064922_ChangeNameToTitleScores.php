<?php
use Migrations\AbstractMigration;

class ChangeNameToTitleScores extends AbstractMigration
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
        $table = $this->table('title_scores');
        $table->changeColumn('name', 'string', [
            'comment' => '対局名',
            'default' => null,
            'limit' => 100,
            'null' => true,
        ]);
        $table->update();
    }
}
