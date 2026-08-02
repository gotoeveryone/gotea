<?php
use Migrations\BaseMigration;

class AddResultToTitleScores extends BaseMigration
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
        $this->table('title_scores')
            ->addColumn('result', 'string', [
                'comment' => '結果',
                'default' => null,
                'limit' => 30,
                'null' => true,
                'after' => 'name',
            ])
            ->update();
    }
}
