<?php
use Migrations\AbstractMigration;

class CreatePlayerRanks extends AbstractMigration
{
    /**
     * 自動で主キーカラムを生成しない
     *
     * @var boolean
     */
    public bool $autoId = false;

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $table = $this->table('player_ranks', [
            'comment' => '棋士昇段情報',
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
            ->addColumn('player_id', 'integer', [
                'comment' => '棋士ID',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('rank_id', 'integer', [
                'comment' => '段位ID',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('promoted', 'date', [
                'comment' => '昇段日',
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
            ->addColumn('modified', 'datetime', [
                'comment' => '更新日時',
                'default' => null,
                'limit' => null,
                'null' => false,
            ]);

        $table->create();

        $this->table('player_ranks')
            ->addForeignKey(
                'player_id',
                'players',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'rank_id',
                'ranks',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();
    }

    public function down()
    {
        $this->table('player_ranks')
            ->dropForeignKey(
                'player_id'
            )
            ->dropForeignKey(
                'rank_id'
            );

        $this->dropTable('player_ranks');
    }
}
