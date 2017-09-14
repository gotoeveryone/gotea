<?php
namespace App\Model\Table;

use Cake\Database\Query;
use Cake\ORM\ResultSet;
use Cake\ORM\RulesChecker;
use Cake\ORM\TableRegistry;

/**
 * TitleScores Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Titles
 * @property \Cake\ORM\Association\HasMany $TitleScoreDetails
 *
 * @method \App\Model\Entity\TitleScore get($primaryKey, $options = [])
 * @method \App\Model\Entity\TitleScore newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\TitleScore[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\TitleScore|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TitleScore patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\TitleScore[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\TitleScore findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TitleScoresTable extends AppTable
{
    /**
     * {@inheritdoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->belongsTo('Titles', [
            'foreignKey' => 'title_id'
        ]);
        $this->belongsTo('Countries', [
            'foreignKey' => 'country_id'
        ]);
        $this->hasMany('TitleScoreDetails', [
            'joinType' => 'INNER',
            'foreignKey' => 'title_score_id',
        ]);
        $this->hasOne('WinDetails', [
            'className' => 'TitleScoreDetails',
            'foreignKey' => 'title_score_id',
            'conditions' => [
                'WinDetails.division' => '勝'
            ]
        ]);
        $this->hasOne('LoseDetails', [
            'className' => 'TitleScoreDetails',
            'foreignKey' => 'title_score_id',
            'conditions' => [
                'LoseDetails.division' => '敗'
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['title_id'], 'Titles'));

        return $rules;
    }

    /**
     * タイトル勝敗を検索します。
     *
     * @param array $data
     * @param boolean $isCount
     * @return TitleScore|int タイトル勝敗一覧|件数
     */
    public function findMatches(array $data, $isCount = false)
    {
        $query = $this->find()
                ->contain([
                    'Countries',
                    'WinDetails', 'WinDetails.Winner', 'WinDetails.Winner.Ranks',
                    'LoseDetails', 'LoseDetails.Loser', 'LoseDetails.Loser.Ranks'])
                ->orderDesc('started')->orderDesc('TitleScores.id');

        if (($id = $data['player_id'] ?? '')) {
            $query->where(['Winner.id' => $id])->orWhere(['Loser.id' => $id]);
        }
        if (($name = trim($data['name'] ?? ''))) {
            $query->where(['Winner.name like ' => "%{$name}%"])->orWhere(['Loser.name like ' => "%{$name}%"]);
        }
        if (($year = $data['target_year'] ?? '')) {
            $query->where(['YEAR(TitleScores.started)' => $year])->where(['YEAR(TitleScores.ended)' => $year]);
        }
        if (($countryId = $data['country_id'] ?? '')) {
            $query->where(['TitleScores.country_id' => $countryId]);
        }
        if (($started = $data['started'] ?? 0)) {
            $query->where(['TitleScores.started >= ' => $started]);
        }
        if (($ended = $data['ended'] ?? 0)) {
            $query->where(['TitleScores.ended <= ' => $ended]);
        }

        if ($isCount) {
            return $query->count();
        }

        return $query->all();
    }

    /**
     * 指定した棋士の年度別成績を取得します。
     *
     * @param mixed $ids
     * @param int|array $years
     * @return \Cake\ORM\ResultSet 成績情報
     */
    public function findFromYear($ids, $years = []) : ResultSet
    {
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        if (!is_array($years)) {
            $years = [$years];
        }
        $q = $this->find()
            ->select(['player_id' => 'TitleScoreDetails.player_id', 'target_year' => 'YEAR(started)', 'win_point' => 'coalesce(win.cnt, 0)',
                'lose_point' => 'coalesce(lose.cnt, 0)', 'draw_point' => 'coalesce(draw.cnt, 0)',
                'win_point_world' => 'coalesce(win_world.cnt, 0)', 'lose_point_world' => 'coalesce(lose_world.cnt, 0)',
                'draw_point_world' => 'coalesce(draw_world.cnt, 0)'])
            ->innerJoin(['TitleScoreDetails' => 'title_score_details'], ['TitleScoreDetails.title_score_id = TitleScores.id'])
            ->leftJoin(['win' => $this->__createSub('勝')], [
                'YEAR(started) = win.target_year',
                'TitleScoreDetails.player_id = win.player_id',
            ])
            ->leftJoin(['lose' => $this->__createSub('敗')], [
                'YEAR(started) = lose.target_year',
                'TitleScoreDetails.player_id = lose.player_id',
            ])
            ->leftJoin(['draw' => $this->__createSub('分')], [
                'YEAR(started) = draw.target_year',
                'TitleScoreDetails.player_id = draw.player_id',
            ])
            ->leftJoin(['win_world' => $this->__createSub('勝', true)], [
                'YEAR(started) = win_world.target_year',
                'TitleScoreDetails.player_id = win_world.player_id',
            ])
            ->leftJoin(['lose_world' => $this->__createSub('敗', true)], [
                'YEAR(started) = lose_world.target_year',
                'TitleScoreDetails.player_id = lose_world.player_id',
            ])
            ->leftJoin(['draw_world' => $this->__createSub('分', true)], [
                'YEAR(started) = draw_world.target_year',
                'TitleScoreDetails.player_id = draw_world.player_id',
            ])
            ->where(['TitleScoreDetails.player_id IN ' => $ids])
            ->group(['TitleScoreDetails.player_id', 'target_year', 'win_point', 'lose_point', 'draw_point', 'win_point_world', 'lose_point_world', 'draw_point_world'])
            ->orderDesc('target_year');

        if (!$years) {
            return $q->all();
        }

        return $q->where(['YEAR(started) IN' => $years])->all();
    }

    /**
     * サブクエリを作成します。
     *
     * @param string $division
     * @param bool $world
     * @return \Cake\Database\Query
     */
    private function __createSub(string $division, $world = false) : Query
    {
        $titleScoreDetails = TableRegistry::get('TitleScoreDetails');
        $sub = $titleScoreDetails->find()
                ->select(['player_id' => 'player_id', 'target_year' => 'YEAR(started)', 'cnt' => 'count(*)'])
                ->contain(['TitleScores'])
                ->where(['division' => $division])->group(['player_id', 'YEAR(started)']);

        return ($world ? $sub->where(['is_world' => true]) : $sub);
    }
}
