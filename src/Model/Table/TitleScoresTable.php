<?php
namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;

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
class TitleScoresTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
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
            'joinType' => 'INNER',
            'className' => 'TitleScoreDetails',
            'foreignKey' => 'title_score_id',
            'conditions' => [
                'WinDetails.division' => '勝'
            ]
        ]);
        $this->hasOne('LoseDetails', [
            'joinType' => 'INNER',
            'className' => 'TitleScoreDetails',
            'foreignKey' => 'title_score_id',
            'conditions' => [
                'LoseDetails.division' => '敗'
            ]
        ]);
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
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
                ->leftJoinWith('WinDetails.Winner')
                ->leftJoinWith('LoseDetails.Loser')
                ->orderDesc('started');

        if (isset($data['name']) && ($name = $data['name'])) {
            $query->where(['Winner.name like ' => "%{$name}%"])->orWhere(['Loser.name like ' => "%{$name}%"]);
        }
        if (isset($data['target_year']) && ($year = $data['target_year'])) {
            $query->where(['YEAR(TitleScores.started)' => $year])->where(['YEAR(TitleScores.ended)' => $year]);
        }
        if (isset($data['country_id']) && ($countryId = $data['country_id'])) {
            $query->where(['TitleScores.country_id' => $countryId]);
        }
        if (isset($data['started']) && ($started = $data['started'])) {
            $query->where(['TitleScores.started >= ' => $started]);
        }
        if (isset($data['ended']) && ($ended = $data['ended'])) {
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
     * @param $playerId
     * @return array
     */
    public function findFromYear(int $playerId)
    {
        return $this->find()
            ->select(['target_year' => 'YEAR(started)', 'win_point' => 'coalesce(win.cnt, 0)',
                'lose_point' => 'coalesce(lose.cnt, 0)', 'draw_point' => 'coalesce(draw.cnt, 0)',
                'win_point_world' => 'coalesce(win_world.cnt, 0)', 'lose_point_world' => 'coalesce(lose_world.cnt, 0)',
                'draw_point_world' => 'coalesce(draw_world.cnt, 0)'])
            ->leftJoin(['win' => $this->__createSub($playerId, '勝')], ['YEAR(started) = win.target_year'])
            ->leftJoin(['lose' => $this->__createSub($playerId, '敗')], ['YEAR(started) = lose.target_year'])
            ->leftJoin(['draw' => $this->__createSub($playerId, '分')], ['YEAR(started) = draw.target_year'])
            ->leftJoin(['win_world' => $this->__createSub($playerId, '勝', true)], ['YEAR(started) = win_world.target_year'])
            ->leftJoin(['lose_world' => $this->__createSub($playerId, '敗', true)], ['YEAR(started) = lose_world.target_year'])
            ->leftJoin(['draw_world' => $this->__createSub($playerId, '分', true)], ['YEAR(started) = draw_world.target_year'])
            ->group(['target_year', 'win_point', 'lose_point', 'draw_point', 'win_point_world', 'lose_point_world', 'draw_point_world'])
            ->orderDesc('target_year');
    }

    /**
     * サブクエリを作成します。
     * 
     * @param $playerId
     * @param string $division
     * @param bool $world
     * @return \Cake\Database\Query
     */
    private function __createSub(int $playerId, string $division, bool $world = false) : \Cake\Database\Query
    {
        $titleScoreDetails = \Cake\ORM\TableRegistry::get('TitleScoreDetails');
        $sub = $titleScoreDetails->find()
                ->select(['player_id' => 'player_id', 'target_year' => 'YEAR(started)', 'cnt' => 'count(*)'])
                ->contain(['TitleScores'])
                ->where(['division' => $division])->group(['player_id', 'YEAR(started)'])
                ->innerJoinWith('Players', function(\Cake\Database\Query $q) use ($playerId) {
                    return $q->where(['Players.id' => $playerId]);
                });

        if ($world) {
            $sub->where(['is_world' => true]);
        }
        return $sub;
    }
}
