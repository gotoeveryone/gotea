<?php

use Migrations\AbstractMigration;

class ChangeColumnToTitles extends AbstractMigration
{
    /**
     * Up Method.
     *
     * @return void
     */
    public function up()
    {
        $this->table('titles')
            ->changeColumn('name_english', 'string', [
                'comment' => 'タイトル名（英語）',
                'default' => null,
                'limit' => 60,
                'null' => false,
            ])
            ->changeColumn('html_file_name', 'string', [
                'comment' => 'htmlファイル名',
                'default' => null,
                'limit' => 30,
                'null' => false,
            ])
            ->update();
    }

    /**
     * Down Method.
     *
     * @return void
     */
    public function down()
    {
        $this->table('title_scores')
            ->changeColumn('name_english', 'string', [
                'comment' => 'タイトル名（英語）',
                'default' => null,
                'limit' => 30,
                'null' => false,
            ])
            ->changeColumn('html_file_name', 'string', [
                'comment' => 'htmlファイル名',
                'default' => null,
                'limit' => 10,
                'null' => false,
            ])
            ->update();
    }
}
