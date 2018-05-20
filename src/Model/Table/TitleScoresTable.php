<?php
namespace Gotea\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\Utility\Hash;

/**
 * TitleScores Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Titles
 * @property \Cake\ORM\Association\BelongsTo $Countries
 * @property \Cake\ORM\Association\HasMany $TitleScoreDetails
 *
 * @method \Gotea\Model\Entity\TitleScore get($primaryKey, $options = [])
 * @method \Gotea\Model\Entity\TitleScore newEntity($data = null, array $options = [])
 * @method \Gotea\Model\Entity\TitleScore[] newEntities(array $data, array $options = [])
 * @method \Gotea\Model\Entity\TitleScore|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Gotea\Model\Entity\TitleScore patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \Gotea\Model\Entity\TitleScore[] patchEntities($entities, array $data, array $options = [])
 * @method \Gotea\Model\Entity\TitleScore findOrCreate($search, callable $callback = null, $options = [])
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

        $this->belongsTo('Titles')
            ->setJoinType('INNER');
        $this->belongsTo('Countries')
            ->setJoinType('INNER');
        $this->hasMany('TitleScoreDetails');
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
     * @param array $data パラメータ
     * @return \Cake\ORM\Query 生成されたクエリ
     */
    public function findMatches(array $data) : Query
    {
        $query = $this->find()
                ->contain([
                    'Countries',
                    'TitleScoreDetails',
                    'TitleScoreDetails.Players',
                    'TitleScoreDetails.Ranks',
                ])
                ->orderDesc('started')->orderDesc('TitleScores.id');

        if (($id = Hash::get($data, 'player_id'))) {
            $query->leftJoinWith('TitleScoreDetails', function (Query $q) use ($id) {
                return $q->innerJoinWith('Players', function (Query $q) use ($id) {
                    return $q->where(['TitleScoreDetails.player_id' => $id]);
                });
            });
        }
        if (($name = Hash::get($data, 'name'))) {
            $query->leftJoinWith('TitleScoreDetails', function (Query $q) use ($name) {
                return $q->innerJoinWith('Players', function (Query $q) use ($name) {
                    return $q->where(['Players.name like' => "%${name}%"]);
                });
            });
        }
        if (($titleName = Hash::get($data, 'title_name'))) {
            $query->where(['TitleScores.name like' => "%${titleName}%"]);
        }
        if (($year = Hash::get($data, 'target_year'))) {
            $query->where(['YEAR(TitleScores.started)' => $year])->where(['YEAR(TitleScores.ended)' => $year]);
        }
        if (($countryId = Hash::get($data, 'country_id'))) {
            $query->where(['TitleScores.country_id' => $countryId]);
        }
        if (($started = Hash::get($data, 'started', 0)) > 0) {
            $query->where(['TitleScores.started >= ' => $started]);
        }
        if (($ended = Hash::get($data, 'ended', 0)) > 0) {
            $query->where(['TitleScores.ended <= ' => $ended]);
        }

        return $query;
    }
}
