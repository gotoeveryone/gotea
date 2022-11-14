<?php
declare(strict_types=1);

namespace Gotea\Model\Entity;

use Cake\ORM\TableRegistry;

/**
 * 段位の操作クラス
 */
trait RankTrait
{
    /**
     * 段位を取得します。
     *
     * @param \Gotea\Model\Entity\Rank|null $rank 段位オブジェクト
     * @return \Gotea\Model\Entity\Rank
     */
    protected function _getRank(?Rank $rank): Rank
    {
        if ($rank) {
            return $rank;
        }

        if (!$this->rank_id) {
            return null;
        }

        $result = TableRegistry::getTableLocator()->get('Ranks')->get($this->rank_id);

        $this->rank = $result;

        return $this->rank;
    }
}
