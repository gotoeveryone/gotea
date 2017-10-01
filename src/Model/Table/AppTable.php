<?php

namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * アプリケーションの共通テーブル
 */
class AppTable extends Table
{
    /**
     * {@inheritdoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        // カスタムバリデータクラスに変更
        $this->_validatorClass = '\App\Validation\MyValidator';

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
