<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class BackfillJoinedDatePartsOnPlayers extends AbstractMigration
{
    /**
     * @return void
     */
    public function up()
    {
        $this->execute("
            UPDATE players
               SET joined_year = CAST(SUBSTR(joined, 1, 4) AS INTEGER)
             WHERE joined REGEXP '^[0-9]{4,8}$'
        ");

        $this->execute("
            UPDATE players
               SET joined_month = CAST(SUBSTR(joined, 5, 2) AS INTEGER)
             WHERE joined REGEXP '^[0-9]{6,8}$'
        ");

        $this->execute("
            UPDATE players
               SET joined_day = CAST(SUBSTR(joined, 7, 2) AS INTEGER)
             WHERE joined REGEXP '^[0-9]{8}$'
        ");
    }

    /**
     * @return void
     */
    public function down()
    {
        $this->execute("
            UPDATE players
               SET joined_year = NULL,
                   joined_month = NULL,
                   joined_day = NULL
        ");
    }
}
