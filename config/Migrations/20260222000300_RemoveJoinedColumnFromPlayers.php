<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class RemoveJoinedColumnFromPlayers extends AbstractMigration
{
    /**
     * @return void
     */
    public function up()
    {
        $table = $this->table('players');
        $table
            ->changeColumn('joined_year', 'smallinteger', [
                'comment' => '入段年',
                'default' => null,
                'null' => false,
            ])
            ->removeColumn('joined')
            ->update();
    }

    /**
     * @return void
     */
    public function down()
    {
        $table = $this->table('players');
        $table
            ->addColumn('joined', 'string', [
                'comment' => '入段日',
                'default' => null,
                'limit' => 8,
                'null' => false,
                'after' => 'sex',
            ])
            ->changeColumn('joined_year', 'smallinteger', [
                'comment' => '入段年',
                'default' => null,
                'null' => true,
            ])
            ->update();

        $this->execute("
            UPDATE players
               SET joined = CONCAT(
                    LPAD(joined_year, 4, '0'),
                    IF(joined_month IS NULL, '', LPAD(joined_month, 2, '0')),
                    IF(joined_day IS NULL, '', LPAD(joined_day, 2, '0'))
               )
        ");
    }
}
