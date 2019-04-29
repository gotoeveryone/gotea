<?php
use Migrations\AbstractMigration;

class AddIsOutputToTitles extends AbstractMigration
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
        $table = $this->table('titles');
        $table->addColumn('is_output', 'boolean', [
            'comment' => 'Go News 出力有無',
            'default' => true,
            'limit' => null,
            'null' => false,
            'after' => 'is_closed',
        ]);
        $table->update();
    }
}
