<?php

namespace App\Model\Table;

use Cake\Validation\Validator;
use Cake\ORM\Query;
use App\Model\Entity\Title;

/**
 * タイトル
 */
class TitlesTable extends AppTable {

    /**
	 * 初期設定
     * 
     * @param $config
	 */
    public function initialize(array $config)
    {
        // タイトル保持情報
        $this->hasMany('RetentionHistories', [
            'order' => [
                'id' => 'ASC',
                'RetentionHistories.holding' => 'DESC'
            ]
        ]);
        // 所属国マスタ
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
            ->notEmpty('name', $this->getMessage($this->REQUIRED, 'タイトル名'))
            ->notEmpty('name_english', $this->getMessage($this->REQUIRED, 'タイトル名（英語）'))
            ->add('name_english', 'default', [
                'rule' => [$this, 'alphaNumeric'],
                'message' => $this->getMessage($this->ALPHA_NUMERIC, 'タイトル名（英語）')
            ])
            ->notEmpty('holding', $this->getMessage($this->REQUIRED, '期'))
            ->numeric('holding', $this->getMessage($this->NUMERIC, '期'))
            ->notEmpty('sort_order',$this->getMessage($this->REQUIRED, '並び順'))
            ->numeric('sort_order', $this->getMessage($this->NUMERIC, '並び順'))
            ->notEmpty('html_file_name', $this->getMessage($this->REQUIRED, 'HTMLファイル名'))
            ->add('html_file_name', 'default', [
                'rule' => [$this, 'alphaNumeric'],
                'message' => $this->getMessage($this->ALPHA_NUMERIC, 'HTMLファイル名')
            ])
            ->notEmpty('html_file_modified', $this->getMessage($this->REQUIRED, 'HTMLファイル修正日'))
            ->date('html_file_modified', ['ymd'], $this->getMessage($this->INLALID_FORMAT, ['修正日', 'yyyy/mm/dd']));
    }

    /**
     * 所属国をもとにタイトルの一覧を取得します。
     * 
     * @param array $data
     * @return type
     */
    public function findTitlesByCountry($data = [])
    {
        $query = $this->find()->contain([
            'Countries',
            'RetentionHistories' => function (Query $q) {
                return $q->where(['RetentionHistories.holding = Titles.holding']);
            },
            'RetentionHistories.Titles',
            'RetentionHistories.Players',
            'RetentionHistories.Ranks'
        ]);

        // 所属国があれば条件追加
        if (isset($data['country_id']) && ($countryId = $data['country_id'])) {
            $query->where(['Countries.id' => $countryId]);
        }

        // 有効なタイトルのみ検索
        if (!isset($data['is_closed']) || !$data['is_closed']) {
            $query->where(['Titles.is_closed' => 0]);
        }

        // データを取得
        return $query->order(['Titles.country_id', 'Titles.sort_order'])->all();
    }

    /**
     * タイトル情報一式を取得
     * 
     * @param type $id
     * @return type
     */
    public function getInner($id)
    {
		return $this->find()->contain([
            'Countries',
            'RetentionHistories' => function (Query $q) {
                return $q->orderDesc('RetentionHistories.target_year');
            },
            'RetentionHistories.Ranks',
            'RetentionHistories.Titles.Countries',
            'RetentionHistories.Players'
        ])->where(['Titles.id' => $id])->first();
    }

    /**
     * モデルを配列に変換します。
     * 
     * @param type $models
     * @param bool $admin 管理者情報を取得するか
     * @param bool $isJp 日本語情報を取得するか
     * @return array
     */
    public function toArray($models, $admin = false, $isJp = false) : array
    {
        $res = [];
        foreach ($models as $model) {
            $row = [
                'countryName' => $model->country->name_english,
                'countryNameAbbreviation' => $model->country->code,
                'titleName' => $model->name_english,
                'holding' => $model->holding,
                'isTeam' => $model->is_team,
                'winnerName' => $model->getWinnerName($isJp),
                'htmlFileName' => $model->html_file_name,
                'htmlFileModified' => $model->html_file_modified->format(($admin ? 'Y/m/d' : 'Y-m-d')),
                'isNewHistories' => $model->isNewHistories(),
                'isRecent' => $model->isRecentModified(),
            ];

            if ($admin) {
                $row['titleId'] = $model->id;
                $row['countryId'] = $model->country_id;
                $row['sortOrder'] = $model->sort_order;
                $row['titleNameJp'] = $model->name;
                $row['isClosed'] = $model->is_closed;
            }

            $res[] = $row;
        }

        return $res;
    }

    /**
     * 配列からモデルデータを生成します。
     *
     * @param array $data
     * @return Title
     */
    public function fromArray($data = []) : Title
    {
        $title = (isset($data['titleId'])) ? $this->get($data['titleId']) : $this->newEntity();

        // 入力値の変更
        $title->name = $data['titleNameJp'] ?? '';
        $title->name_english = $data['titleName'] ?? '';
        $title->country_id = $data['countryId'] ?? '';
        $title->sort_order = $data['sortOrder'] ?? 1;
        $title->holding = $data['holding'] ?? '';
        $title->is_team = $data['isTeam'] ?? false;
        $title->html_file_name = $data['htmlFileName'] ?? '';
        $title->html_file_modified = $data['htmlFileModified'] ?? '';
        $title->is_closed = $data['isClosed'] ?? false;

        return $title;
    }
}
