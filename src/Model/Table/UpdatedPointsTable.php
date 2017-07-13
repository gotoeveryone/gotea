<?php

namespace App\Model\Table;

use App\Model\Entity\Country;

/**
 * 成績更新
 */
class UpdatedPointsTable extends AppTable
{
    /**
     * 所属国と対象年から最終更新日を取得します。
     *
     * @param \App\Model\Entity\Country $country
     * @param int $targetYear
     * @return string
     */
    public function findRecent(Country $country, int $targetYear) : string
    {
        $target = $this->find()->where([
            'country_id' => $country->id,
            'target_year' => $targetYear,
        ])->first();

        if (!$target) {
            return '';
        }

        return $target->score_updated->format('Y-m-d');
    }
}
