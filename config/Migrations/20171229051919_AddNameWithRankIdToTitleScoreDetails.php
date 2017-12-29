<?php
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\TableRegistry;
use Migrations\AbstractMigration;

/**
 * タイトル成績詳細へカラム追加（棋士名・タイトルID）
 */
class AddNameWithRankIdToTitleScoreDetails extends AbstractMigration
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
        $table = $this->table('title_score_details');
        $table->addColumn('rank_id', 'integer', [
            'comment' => '段位ID',
            'default' => null,
            'limit' => 11,
            'null' => false,
            'after' => 'player_id',
        ])
        ->addColumn('player_name', 'string', [
            'comment' => '棋士名',
            'default' => null,
            'limit' => 20,
            'null' => true,
            'after' => 'rank_id',
        ]);
        $table->update();

        // 段位IDを更新
        $exp = [
            new QueryExpression('player_name = (select name from players where id = player_id)'),
            new QueryExpression('rank_id = (select rank_id from players where id = player_id)'),
        ];
        $details = TableRegistry::get('title_score_details');
        $details->updateAll($exp, []);

        // 参照キー追加
        $table
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
}
