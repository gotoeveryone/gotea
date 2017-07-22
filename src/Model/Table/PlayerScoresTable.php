<?php

namespace App\Model\Table;

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
        parent::initialize($config);

        $this->belongsTo('Players');
        $this->belongsTo('Ranks');
    }
}
