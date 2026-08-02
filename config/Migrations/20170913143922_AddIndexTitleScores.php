<?php
use Migrations\BaseMigration;

class AddIndexTitleScores extends BaseMigration
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
        $table->addIndex('started', [
            'name' => 'idx_started',
        ]);
        $table->addIndex('ended', [
            'name' => 'idx_ended',
        ]);
        $table->update();
    }
}
