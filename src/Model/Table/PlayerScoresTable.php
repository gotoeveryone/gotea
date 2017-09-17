<?php

namespace App\Model\Table;

/**
 * 棋士成績
 */
class PlayerScoresTable extends AppTable
{
    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->belongsTo('Ranks');
    }

    /**
     * 対象棋士の成績一覧を取得します。
     *
     * @param int $id
     * @return \Cake\ORM\ResultSet
     */
    public function findDescYears(int $id)
    {
        return $this->findByPlayerId($id)
            ->contain(['Ranks'])->orderDesc('target_year')->all();
    }
}
