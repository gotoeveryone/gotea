<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateUsers extends AbstractMigration
{
    /**
     * 自動で主キーカラムを生成しない
     *
     * @var bool
     */
    public $autoId = false;

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
        $table = $this->table('users', [
            'comment' => 'ユーザ',
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
            ->addColumn('account', 'string', [
                'comment' => 'アカウント',
                'default' => null,
                'limit' => 10,
                'null' => false,
            ])
            ->addColumn('name', 'string', [
                'comment' => 'ユーザ名',
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('password', 'string', [
                'comment' => 'パスワード',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('is_admin', 'boolean', [
                'comment' => '管理者フラグ',
                'default' => false,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('last_logged', 'datetime', [
                'comment' => '最終ログイン日時',
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
            ])
            ->addIndex(
                [
                    'account',
                ],
                ['unique' => true]
            );
        $table->create();
    }
}
