<?php
use Migrations\AbstractMigration;

class AddHasMenuToTitles extends AbstractMigration
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
        $table->addColumn('has_menu', 'boolean', [
            'comment' => 'メニュー保持有無',
            'default' => true,
            'limit' => null,
            'null' => false,
            'after' => 'is_closed',
        ]);
        $table->update();
    }
}
