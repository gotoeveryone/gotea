<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use App\Validation\MyValidationTrait;

/**
 * アプリケーションの共通テーブル
 */
class AppTable extends Table
{
    use MyValidationTrait;

    /**
     * {@inheritdoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->addBehavior('Timestamp');
        $this->addBehavior('SaveUser');
    }

    /**
     * ランキングデータの取得方法を判定します。
     *
     * @param int $targetYear
     * @return bool
     */
    protected function _isOldRanking(int $targetYear) : bool
    {
        return ($targetYear < 2017);
    }
}
