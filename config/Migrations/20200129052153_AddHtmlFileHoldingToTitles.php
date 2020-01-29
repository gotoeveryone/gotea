<?php
use Migrations\AbstractMigration;

class AddHtmlFileHoldingToTitles extends AbstractMigration
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
            ->addColumn('html_file_holding', 'integer', [
                'comment' => 'htmlファイル期',
                'default' => null,
                'limit' => 3,
                'null' => true,
                'after' => 'html_file_name',
            ])
            ->update();
    }
}
