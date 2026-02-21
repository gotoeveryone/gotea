<?php
declare(strict_types=1);

namespace Gotea\Model\Table;

use Cake\ORM\Query;
use Gotea\Model\Entity\Rank;

/**
 * 段位
 */
class RanksTable extends AppTable
{
    /**
     * @inheritDoc
     */
    public function initialize(array $config): void
    {
        $this->setDisplayField('name');
    }

    /**
     * 段位のID・名前を一覧で取得します。
     *
     * @return \Cake\ORM\Query 生成されたクエリ
     */
    public function findProfessional(): Query
    {
        return $this->find()->whereNotNull('rank_numeric')
            ->orderByDesc('rank_numeric')->select(['id', 'name', 'rank_numeric']);
    }

    /**
     * 対象段位に該当する段位情報を取得します。
     *
     * @param int $rank 段位
     * @return \Gotea\Model\Entity\Rank
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function findByRank(int $rank = 1): Rank
    {
        return $this->findByRankNumeric($rank)->firstOrFail();
    }
}
