<?php
namespace Gotea\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;

/**
 * TitleScores Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Titles
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
        $this->hasOne('WinDetails', ['className' => 'TitleScoreDetails'])
            ->setForeignKey('title_score_id')
            ->setConditions([
                'WinDetails.division' => '勝',
            ]);
        $this->hasOne('LoseDetails', ['className' => 'TitleScoreDetails'])
            ->setForeignKey('title_score_id')
            ->setConditions([
                'LoseDetails.division' => '敗',
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
     * @return \Cake\ORM\Query 生成されたクエリ
     */
    public function findMatches(array $data) : Query
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

        return $query;
    }
}
