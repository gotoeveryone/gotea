<?php

namespace App\Model\Table;

/**
 * 所属国
 */
class CountriesTable extends AppTable
{
    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        $this->displayField('name');
    }

    /**
     * 所属国一覧（ID・名前）を取得します。
     *
     * @param bool $hasTitle
     * @return array
     */
    public function findToKeyValue($hasTitle = false)
    {
		$query = $this->find('list');

        if ($hasTitle) {
            $query->where('has_title', true);
        }

        return $query->order(['id'])->toArray();
    }
}
