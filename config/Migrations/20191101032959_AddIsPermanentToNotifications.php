<?php

use Migrations\AbstractMigration;

class AddIsPermanentToNotifications extends AbstractMigration
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
        $this->table('notifications')
            ->addColumn('is_permanent', 'boolean', [
                'comment' => '恒久表示フラグ',
                'default' => false,
                'limit' => null,
                'null' => false,
                'after' => 'published',
            ])
            ->update();
    }
}
