<?php

namespace Gotea\Model\Table;

use Cake\ORM\Table;

/**
 * アプリケーションの共通テーブル
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \Gotea\Model\Behavior\SaveUserBehavior
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
        $this->_validatorClass = '\Gotea\Validation\MyValidator';

        $this->addBehavior('Timestamp');
        $this->addBehavior('SaveUser');
    }

    /**
     * 指定された条件でテーブルを検索し、存在しなければ条件を生成したモデルを返却します。
     *
     * @param array $options オプション
     * @return \Cake\Datasource\EntityInterface
     */
    public function findOrNew(array $options)
    {
        $model = $this->find()->where($options)->first();
        if ($model !== null) {
            return $model;
        }

        return $this->newEntity($options);
    }

    /**
     * ランキングデータの取得方法を判定します。
     *
     * @param int $targetYear 対象年度
     * @return bool
     */
    protected function _isOldRanking(int $targetYear) : bool
    {
        return ($targetYear < 2017);
    }
}
