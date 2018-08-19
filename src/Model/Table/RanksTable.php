<?php

namespace Gotea\Model\Table;

/**
 * 段位
 */
class RanksTable extends AppTable
{
    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        $this->setDisplayField('name');
    }

    /**
     * 段位のID・名前を一覧で取得します。
     *
     * @return \Cake\ORM\Query 生成されたクエリ
     */
    public function findProfessional()
    {
        // 段位プルダウン
        return $this->find()->where([
            'rank_numeric IS NOT' => null
        ])->order('rank_numeric DESC')->select(['id', 'name', 'rank_numeric']);
    }

    /**
     * 対象段位に該当する段位情報を取得します。
     *
     * @param int $rank 段位
     * @return \Gotea\Model\Entity\Rank
     * @throws \Cake\Datasource\Exception\InvalidPrimaryKeyException
     */
    public function findByRank($rank = 1)
    {
        return $this->findByRankNumeric($rank)->firstOrFail();
    }
}
