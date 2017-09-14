<?php
use Migrations\AbstractMigration;

class AddIndexTitleScoreDetails extends AbstractMigration
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
        $table = $this->table('title_score_details');
        $table->addIndex('division', [
            'name' => 'idx_division',
        ]);
        $table->update();
    }
}
