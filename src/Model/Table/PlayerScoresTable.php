<?php

namespace App\Model\Table;

/**
 * 棋士成績情報
 */
class PlayerScoresTable extends AppTable
{
    /**
	 * 初期設定
	 */
    public function initialize(array $config)
    {
        $this->table('T_PLAYER_SCORE');
        $this->primaryKey('ID');
//        $this->entityClass('App\Model\Entity\Player');
//        $this->hasMany('Player', [
//            'className' => 'Player',
//            'foreignKey' => 'PLAYER_ID',
//            'type' => 'INNER'
//        ]);
        $this->belongsTo('Ranks', [
            'foreignKey' => 'RANK_ID',
            'joinType' => 'INNER'
        ]);
    }

    /**
     * 棋士IDと対象年で棋士成績情報を1件取得します。
     * 
     * @param type $playerId
     * @param type $targetYear
     * @return type
     */
    public function findByPlayerAndYear($playerId, $targetYear)
    {
        $score = $this->find()->where([
            'PLAYER_ID' => $playerId,
            'TARGET_YEAR' => $targetYear
        ])->first();

        if (!$score) {
            $score = $this->newEntity();
            $score->set('PLAYER_ID', $playerId);
            $score->set('TARGET_YEAR', $targetYear);
        }

        return $score;
    }

    /**
     * 成績更新日情報の取り扱い念を取得します。
     * 
     * @param type $targetYear
     * @return type
     */
    public function findScoreUpdateToArrayWithSuffix()
    {
        return $this->find('list', [
            'keyField' => 'keyField',
            'valueField' => 'valueField'
        ])->group(['PlayerScores.TARGET_YEAR'])->order(['PlayerScores.TARGET_YEAR' => 'DESC'])->select([
            'keyField' => 'PlayerScores.TARGET_YEAR',
            'valueField' => 'CONCAT(PlayerScores.TARGET_YEAR, \'年度\')'
        ])->toArray();
    }
}
