<?php
use Migrations\BaseMigration;

class RemoveHasMenuToTitle extends BaseMigration
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
        $table->removeColumn('has_menu');
        $table->update();
    }
}
