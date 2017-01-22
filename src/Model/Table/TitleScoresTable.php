<?php
namespace App\Model\Table;

use Cake\Database\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

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
     * タイトル成績を検索します。
     * 
     * @param int $countryId
     * @param string|null $started
     * @param string|null $ended
     */
    public function findMatches(int $countryId, $name = null, $started = null, $ended = null)
    {
        $query = $this->find()
                ->contain([
                    'Countries',
                    'WinDetails', 'WinDetails.Winner', 'WinDetails.Winner.Ranks',
                    'LoseDetails', 'LoseDetails.Loser', 'LoseDetails.Loser.Ranks'])
                ->innerJoinWith('WinDetails.Winner')
                ->innerJoinWith('LoseDetails.Loser')
                ->orderDesc('started');

        if ($name) {
            $query->where(['Winner.name like ' => "%{$name}%"])
            ->orWhere(['Loser.name like ' => "%{$name}%"]);
        }

        if ($countryId) {
            $query->where(['TitleScores.country_id' => $countryId]);
        }
        if ($started) {
            $query->where(['started >= ' => $started]);
        }

        if ($ended) {
            $query->where(['ended <= ' => $ended]);
        }

        return $query->all();
    }

    /**
     * 指定の区分の棋士名を抽出する
     * 
     * @param type $division
     * @param type $countryId
     * @return type
     */
    private function __createSub($division, $countryId = null)
    {
        // 敗者
        $query = $this->TitleScoreDetails->find()
                ->select('Players.name')
                ->innerJoin('Players', ['TitleScoreDetails.player_id = Players.id'])
                ->where(['division' => $division, 'title_score_id = TitleScores.id']);

        if ($countryId) {
            $query->where(['Players.country_id' => $countryId]);
        }

        return $query;
    }
}
