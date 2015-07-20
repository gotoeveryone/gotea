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
            'foreignKey' => 'PLAYER_RANK',
            'joinType' => 'INNER'
        ]);
    }
}
