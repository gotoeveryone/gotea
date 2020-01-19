<?php
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\TableRegistry;
use Migrations\AbstractMigration;

class RemoveRankIdFromTitleScoreDetails extends AbstractMigration
{
    /**
     * Up Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-up-method
     * @return void
     */
    public function up()
    {
        $table = $this->table('title_score_details');

        // 参照キー削除
        $table
            ->dropForeignKey('rank_id')
            ->update();

        // カラム削除
        $table
            ->removeColumn('rank_id')
            ->update();
    }

    /**
     * Down Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-down-method
     * @return void
     */
    public function down()
    {
        $table = $this->table('title_score_details');

        // カラム追加
        $table
            ->addColumn('rank_id', 'integer', [
                'comment' => '段位ID',
                'default' => null,
                'limit' => 11,
                'null' => false,
                'after' => 'player_id',
            ])
            ->update();

        // 段位IDを更新
        $exp = [
            new QueryExpression('rank_id = (select rank_id from players where id = player_id)'),
        ];
        $details = TableRegistry::getTableLocator()->get('title_score_details');
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
