<?php
declare(strict_types=1);

namespace Gotea\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Table;
use Gotea\Validation\Validator;

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
    // phpcs:ignore
    protected $_validatorClass = Validator::class;

    /**
     * @inheritDoc
     */
    public function initialize(array $config): void
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
    public function findOrNew(array $data, array $options = []): EntityInterface
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
}
