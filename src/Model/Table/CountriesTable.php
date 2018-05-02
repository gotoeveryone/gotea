<?php

namespace Gotea\Model\Table;

/**
 * 所属国
 */
class CountriesTable extends AppTable
{
    /**
     * {@inheritdoc}
     */
    public function initialize(array $config)
    {
        $this->setDisplayField('name');
    }

    /**
     * コードが設定されている所属国一覧を取得します。
     *
     * @param bool $hasTitle タイトルを保持しているか
     * @return \Cake\ORM\Query 生成されたクエリ
     */
    public function findAllHasCode($hasTitle = false)
    {
        $query = $this->find()->where(['code is not' => null]);

        if ($hasTitle) {
            $query->where(['has_title' => true]);
        }

        return $query->select(['id', 'code', 'name', 'name_english', 'has_title']);
    }
}
