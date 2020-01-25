<?php
use Migrations\AbstractMigration;

class AddIsOfficialToTitles extends AbstractMigration
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
        $this->table('titles')
            ->addColumn('is_official', 'boolean', [
                'comment' => '公式戦フラグ',
                'default' => true,
                'limit' => null,
                'null' => false,
                'after' => 'is_output',
            ])
            ->update();
    }
}
