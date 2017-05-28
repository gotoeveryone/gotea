<?php

namespace App\Model\Table;

use Cake\Validation\Validator;
use App\Model\Entity\Country;

/**
 * 成績更新
 */
class UpdatedPointsTable extends AppTable
{
    /**
	 * 初期設定
	 */
    public function initialize(array $config)
    {
        $this->belongsTo('Countries');
    }

    /**
     * バリデーションルール
     * 
     * @param \App\Model\Table\Validator $validator
     * @return type
     */
    public function validationDefault(Validator $validator)
    {
        return $validator
            ->notEmpty('score_updated', $this->getMessage($this->REQUIRED, '成績更新日'))
            ->date('score_updated', 'ymd', $this->getMessage($this->INLALID_FORMAT, ['成績更新日', 'yyyy/MM/dd']));
    }

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

    /**
     * 成績更新日情報の対象年を取得します。
     * 
     * @param int $targetYear
     * @return type
     */
    public function findScoreUpdateHasYear(int $targetYear)
    {
        return $this->find()->where([
            'target_year' => $targetYear
        ])->order([
            'country_id',
            'target_year' => 'DESC'
        ])->contain(['Countries'])->all();
    }

    /**
     * 成績更新日情報の対象年をキー・値の配列にして取得します。
     * 
     * @return array
     */
    public function findToArray() : array
    {
        return $this->find('list', [
            'keyField' => 'keyField',
            'valueField' => 'valueField'
        ])->group(['target_year'])->order(['target_year' => 'DESC'])->select([
            'keyField' => 'target_year',
            'valueField' => 'target_year'
        ])->toArray();
    }
}
