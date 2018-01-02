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
     * カスタムバリデータクラス.
     *
     * @var string
     */
    protected $_validatorClass = '\Gotea\Validation\Validator';

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
     * 指定された条件でテーブルを検索し、存在しなければ条件で生成したモデルを返却します。
     *
     * @param array $data データ
     * @param array $options オプション
     * @return \Cake\Datasource\EntityInterface
     */
    public function findOrNew(array $data, array $options = [])
    {
        $model = $this->find()->where($data)->first();
        if ($model !== null) {
            return $model;
        }

        // ここで生成するエンティティはバリデーションしない
        if (!isset($options['validate'])) {
            $options['validate'] = false;
        }

        return $this->newEntity($data, $options);
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
