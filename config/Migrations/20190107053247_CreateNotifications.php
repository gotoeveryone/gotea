<?php
use Migrations\AbstractMigration;

class CreateNotifications extends AbstractMigration
{
    /**
     * 自動で主キーカラムを生成しない
     *
     * @var boolean
     */
    public $autoId = false;

    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('notifications', [
            'comment' => 'お知らせ',
        ])
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'signed' => false,
                'comment' => 'サロゲートキー',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addPrimaryKey('id')
            ->addColumn('title', 'string', [
                'comment' => 'タイトル',
                'default' => null,
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('content', 'text', [
                'comment' => '本文',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('is_draft', 'boolean', [
                'comment' => '下書き',
                'default' => true,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('published', 'datetime', [
                'comment' => '公開日時',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'comment' => '初回登録日時',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'comment' => '更新日時',
                'default' => null,
                'limit' => null,
                'null' => false,
            ]);
        $table->create();
    }
}
