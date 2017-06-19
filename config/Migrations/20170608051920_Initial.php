<?php
use Migrations\AbstractMigration;

class Initial extends AbstractMigration
{
    public function up()
    {

        $this->table('countries')
            ->addColumn('code', 'string', [
                'comment' => '国名コード（ラテン文字2文字）',
                'default' => null,
                'limit' => 2,
                'null' => true,
            ])
            ->addColumn('name', 'string', [
                'comment' => '国名',
                'default' => null,
                'limit' => 20,
                'null' => false,
            ])
            ->addColumn('name_english', 'string', [
                'comment' => '国名（英語）',
                'default' => null,
                'limit' => 30,
                'null' => false,
            ])
            ->addColumn('has_title', 'boolean', [
                'comment' => '所属棋士有無',
                'default' => false,
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
            ])
            ->addIndex(
                [
                    'name',
                ],
                ['unique' => true]
            )
            ->addIndex(
                [
                    'name_english',
                ],
                ['unique' => true]
            )
            ->create();

        $this->table('organizations')
            ->addColumn('country_id', 'integer', [
                'comment' => '国ID',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('name', 'string', [
                'comment' => '組織名',
                'default' => null,
                'limit' => 20,
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
            ])
            ->addIndex(
                [
                    'name',
                ],
                ['unique' => true]
            )
            ->addIndex(
                [
                    'country_id',
                ]
            )
            ->create();

        $this->table('player_scores')
            ->addColumn('player_id', 'integer', [
                'comment' => '棋士ID',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('rank_id', 'integer', [
                'comment' => '段位ID',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('target_year', 'integer', [
                'comment' => '対象年',
                'default' => null,
                'limit' => 4,
                'null' => false,
            ])
            ->addColumn('win_point', 'integer', [
                'comment' => '勝数',
                'default' => '0',
                'limit' => 3,
                'null' => true,
            ])
            ->addColumn('lose_point', 'integer', [
                'comment' => '敗数',
                'default' => '0',
                'limit' => 3,
                'null' => true,
            ])
            ->addColumn('draw_point', 'integer', [
                'comment' => '引分数',
                'default' => '0',
                'limit' => 3,
                'null' => true,
            ])
            ->addColumn('win_point_world', 'integer', [
                'comment' => '勝数（国際棋戦）',
                'default' => '0',
                'limit' => 3,
                'null' => true,
            ])
            ->addColumn('lose_point_world', 'integer', [
                'comment' => '敗数（国際棋戦）',
                'default' => '0',
                'limit' => 3,
                'null' => true,
            ])
            ->addColumn('draw_point_world', 'integer', [
                'comment' => '引分数（国際棋戦）',
                'default' => '0',
                'limit' => 3,
                'null' => true,
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
            ])
            ->addIndex(
                [
                    'player_id',
                    'target_year',
                ],
                ['unique' => true]
            )
            ->addIndex(
                [
                    'player_id',
                ]
            )
            ->addIndex(
                [
                    'rank_id',
                ]
            )
            ->create();

        $this->table('players')
            ->addColumn('country_id', 'integer', [
                'comment' => '所属国ID',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('rank_id', 'integer', [
                'comment' => '段位ID',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('organization_id', 'integer', [
                'comment' => '所属ID',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('name', 'string', [
                'comment' => '棋士名',
                'default' => null,
                'limit' => 20,
                'null' => false,
            ])
            ->addColumn('name_english', 'string', [
                'comment' => '棋士名（英語）',
                'default' => null,
                'limit' => 40,
                'null' => true,
            ])
            ->addColumn('name_other', 'string', [
                'comment' => '棋士名（その他）',
                'default' => null,
                'limit' => 20,
                'null' => true,
            ])
            ->addColumn('sex', 'string', [
                'comment' => '性別',
                'default' => null,
                'limit' => 2,
                'null' => false,
            ])
            ->addColumn('joined', 'string', [
                'comment' => '入段日',
                'default' => null,
                'limit' => 8,
                'null' => false,
            ])
            ->addColumn('birthday', 'date', [
                'comment' => '生年月日',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('remarks', 'string', [
                'comment' => 'その他備考',
                'default' => null,
                'limit' => 500,
                'null' => true,
            ])
            ->addColumn('is_retired', 'boolean', [
                'comment' => '引退済',
                'default' => false,
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
            ])
            ->addIndex(
                [
                    'country_id',
                    'name',
                    'birthday',
                ],
                ['unique' => true]
            )
            ->addIndex(
                [
                    'country_id',
                ]
            )
            ->addIndex(
                [
                    'organization_id',
                ]
            )
            ->addIndex(
                [
                    'rank_id',
                ]
            )
            ->addIndex(
                [
                    'name',
                ]
            )
            ->create();

        $this->table('ranks')
            ->addColumn('name', 'string', [
                'comment' => '段位',
                'default' => null,
                'limit' => 10,
                'null' => false,
            ])
            ->addColumn('rank_numeric', 'integer', [
                'comment' => '段位（数字）',
                'default' => null,
                'limit' => 2,
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
                    'name',
                ],
                ['unique' => true]
            )
            ->create();

        $this->table('retention_histories')
            ->addColumn('title_id', 'integer', [
                'comment' => 'タイトルID',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('player_id', 'integer', [
                'comment' => '優勝棋士ID',
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('rank_id', 'integer', [
                'comment' => '優勝棋士段位ID',
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('holding', 'integer', [
                'comment' => '期',
                'default' => null,
                'limit' => 3,
                'null' => false,
            ])
            ->addColumn('target_year', 'integer', [
                'comment' => '対象年',
                'default' => null,
                'limit' => 4,
                'null' => false,
            ])
            ->addColumn('name', 'string', [
                'comment' => 'タイトル名',
                'default' => null,
                'limit' => 30,
                'null' => false,
            ])
            ->addColumn('win_group_name', 'string', [
                'comment' => '優勝チーム名',
                'default' => null,
                'limit' => 30,
                'null' => true,
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
            ])
            ->addIndex(
                [
                    'title_id',
                    'holding',
                ],
                ['unique' => true]
            )
            ->addIndex(
                [
                    'player_id',
                ]
            )
            ->addIndex(
                [
                    'rank_id',
                ]
            )
            ->addIndex(
                [
                    'title_id',
                ]
            )
            ->create();

        $this->table('title_score_details')
            ->addColumn('title_score_id', 'integer', [
                'comment' => 'タイトル成績ID',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('player_id', 'integer', [
                'comment' => '棋士ID',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('division', 'string', [
                'comment' => '成績区分',
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
            ])
            ->addIndex(
                [
                    'title_score_id',
                    'player_id',
                ],
                ['unique' => true]
            )
            ->addIndex(
                [
                    'player_id',
                ]
            )
            ->addIndex(
                [
                    'title_score_id',
                ]
            )
            ->create();

        $this->table('title_scores')
            ->addColumn('country_id', 'integer', [
                'comment' => '所属国ID',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('title_id', 'integer', [
                'comment' => 'タイトルID',
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('name', 'string', [
                'comment' => '棋戦名',
                'default' => null,
                'limit' => 20,
                'null' => true,
            ])
            ->addColumn('started', 'date', [
                'comment' => '開始日',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('ended', 'date', [
                'comment' => '終了日',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('is_world', 'boolean', [
                'comment' => '国際棋戦かどうか',
                'default' => false,
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
            ])
            ->addIndex(
                [
                    'country_id',
                ]
            )
            ->create();

        $this->table('titles')
            ->addColumn('country_id', 'integer', [
                'comment' => '所属国ID',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('name', 'string', [
                'comment' => 'タイトル名',
                'default' => null,
                'limit' => 30,
                'null' => false,
            ])
            ->addColumn('name_english', 'string', [
                'comment' => 'タイトル名（英語）',
                'default' => null,
                'limit' => 30,
                'null' => false,
            ])
            ->addColumn('holding', 'integer', [
                'comment' => '期',
                'default' => null,
                'limit' => 3,
                'null' => false,
            ])
            ->addColumn('sort_order', 'integer', [
                'comment' => '並び順',
                'default' => null,
                'limit' => 2,
                'null' => false,
            ])
            ->addColumn('html_file_name', 'string', [
                'comment' => 'htmlファイル名',
                'default' => null,
                'limit' => 10,
                'null' => false,
            ])
            ->addColumn('html_file_modified', 'date', [
                'comment' => 'htmlファイル修正日',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('remarks', 'string', [
                'comment' => 'その他備考',
                'default' => null,
                'limit' => 500,
                'null' => true,
            ])
            ->addColumn('is_team', 'boolean', [
                'comment' => '団体戦判定',
                'default' => false,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('is_closed', 'boolean', [
                'comment' => '終了済',
                'default' => false,
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
            ])
            ->addIndex(
                [
                    'country_id',
                    'name',
                    'html_file_name',
                    'is_closed',
                ],
                ['unique' => true]
            )
            ->addIndex(
                [
                    'country_id',
                ]
            )
            ->create();

        $this->table('updated_points')
            ->addColumn('country_id', 'integer', [
                'comment' => '所属国ID',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('target_year', 'integer', [
                'comment' => '対象年',
                'default' => null,
                'limit' => 4,
                'null' => false,
            ])
            ->addColumn('score_updated', 'date', [
                'comment' => '成績情報更新日',
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
            ])
            ->addIndex(
                [
                    'country_id',
                    'target_year',
                ],
                ['unique' => true]
            )
            ->addIndex(
                [
                    'country_id',
                ]
            )
            ->create();

        $this->table('organizations')
            ->addForeignKey(
                'country_id',
                'countries',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->table('player_scores')
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

        $this->table('players')
            ->addForeignKey(
                'country_id',
                'countries',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'organization_id',
                'organizations',
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

        $this->table('retention_histories')
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
            ->addForeignKey(
                'title_id',
                'titles',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->table('title_score_details')
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
                'title_score_id',
                'title_scores',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->table('title_scores')
            ->addForeignKey(
                'country_id',
                'countries',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->table('titles')
            ->addForeignKey(
                'country_id',
                'countries',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->table('updated_points')
            ->addForeignKey(
                'country_id',
                'countries',
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
        $this->table('organizations')
            ->dropForeignKey(
                'country_id'
            );

        $this->table('player_scores')
            ->dropForeignKey(
                'player_id'
            )
            ->dropForeignKey(
                'rank_id'
            );

        $this->table('players')
            ->dropForeignKey(
                'country_id'
            )
            ->dropForeignKey(
                'organization_id'
            )
            ->dropForeignKey(
                'rank_id'
            );

        $this->table('retention_histories')
            ->dropForeignKey(
                'player_id'
            )
            ->dropForeignKey(
                'rank_id'
            )
            ->dropForeignKey(
                'title_id'
            );

        $this->table('title_score_details')
            ->dropForeignKey(
                'player_id'
            )
            ->dropForeignKey(
                'title_score_id'
            );

        $this->table('title_scores')
            ->dropForeignKey(
                'country_id'
            );

        $this->table('titles')
            ->dropForeignKey(
                'country_id'
            );

        $this->table('updated_points')
            ->dropForeignKey(
                'country_id'
            );

        $this->dropTable('countries');
        $this->dropTable('organizations');
        $this->dropTable('player_scores');
        $this->dropTable('players');
        $this->dropTable('ranks');
        $this->dropTable('retention_histories');
        $this->dropTable('title_score_details');
        $this->dropTable('title_scores');
        $this->dropTable('titles');
        $this->dropTable('updated_points');
    }
}
