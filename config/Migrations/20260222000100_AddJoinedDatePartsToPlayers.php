<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddJoinedDatePartsToPlayers extends AbstractMigration
{
    /**
     * @return void
     */
    public function change()
    {
        $table = $this->table('players');
        $table
            ->addColumn('joined_year', 'smallinteger', [
                'comment' => '入段年',
                'default' => null,
                'null' => true,
                'after' => 'joined',
            ])
            ->addColumn('joined_month', 'tinyinteger', [
                'comment' => '入段月',
                'default' => null,
                'null' => true,
                'after' => 'joined_year',
            ])
            ->addColumn('joined_day', 'tinyinteger', [
                'comment' => '入段日',
                'default' => null,
                'null' => true,
                'after' => 'joined_month',
            ])
            ->addIndex(['joined_year'])
            ->addIndex(['joined_year', 'joined_month', 'joined_day'])
            ->update();
    }
}
