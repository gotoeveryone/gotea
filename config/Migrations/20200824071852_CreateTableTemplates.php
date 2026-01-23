<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateTableTemplates extends AbstractMigration
{
    /**
     * 自動で主キーカラムを生成しない
     *
     * @var bool
     */
    public bool $autoId = false;

    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * @return void
     */
    public function change()
    {
        $table = $this->table('table_templates', [
            'comment' => '表テンプレート',
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
                'comment' => 'コンテンツ',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'comment' => '初回登録日時',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('created_by', 'string', [
                'comment' => '初回登録者',
                'default' => null,
                'limit' => 10,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'comment' => '更新日時',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified_by', 'string', [
                'comment' => '最終更新者',
                'default' => null,
                'limit' => 10,
                'null' => false,
            ]);
        $table->create();
    }
}
