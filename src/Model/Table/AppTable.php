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
     * キー情報をもとに、データを1件取得します。
     *
     * @param array $data
     * @param array $fields
     * @return null|\Cake\ORM\Entity
     */
    public function findByKey(array $data, $fields = [])
    {
        $params = [];
        foreach ($fields as $field) {
            if (empty($data[$field])) {
                return null;
            }
            $params[$field] = $data[$field];
        }
        return $this->find()->where($params)->first();
    }

    /**
     * データを追加します。
     *
     * @param array $data
     * @param array $fields
     * @return \App\Model\Entity\AppEntity|false データが登録できればそのEntity
     */
    protected function _addEntity(array $data, $fields = [])
    {
        // 同一キーのデータがあれば終了
        if ($this->findByKey($data, $fields)) {
            return false;
        }

        // データの登録
        return $this->save($this->newEntity($data));
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
