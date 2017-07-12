<?php

namespace App\Model\Table;

use Cake\Validation\Validator;

/**
 * 棋士成績
 */
class PlayerScoresTable extends AppTable
{
    /**
	 * 初期設定
	 */
    public function initialize(array $config)
    {
        $this->belongsTo('Players');
        $this->belongsTo('Ranks');
    }

    /**
     * バリデーションルール
     *
     * @param \App\Model\Table\Validator $validator
     * @return type
     */
    public function validationDefault(Validator $validator)
    {
        return $validator
            ->notEmpty('win_point', $this->getMessage($this->REQUIRED, '勝数'))
            ->numeric('win_point', $this->getMessage($this->NUMERIC, '勝数'))
            ->notEmpty('lose_point',$this->getMessage($this->REQUIRED, '敗数'))
            ->numeric('lose_point', $this->getMessage($this->NUMERIC, '敗数'))
            ->notEmpty('draw_point', $this->getMessage($this->REQUIRED, '引分数'))
            ->numeric('draw_point', $this->getMessage($this->NUMERIC, '引分数'))
            ->notEmpty('win_point_world', $this->getMessage($this->REQUIRED, '勝数（国際棋戦）'))
            ->numeric('win_point_world', $this->getMessage($this->NUMERIC, '勝数（国際棋戦）'))
            ->notEmpty('lose_point_world', $this->getMessage($this->REQUIRED, '敗数（国際棋戦）'))
            ->numeric('lose_point_world', $this->getMessage($this->NUMERIC, '敗数（国際棋戦）'))
            ->notEmpty('draw_point_world', $this->getMessage($this->REQUIRED, '引分数（国際棋戦）'))
            ->numeric('draw_point_world', $this->getMessage($this->NUMERIC, '引分数（国際棋戦）'));
    }
}
