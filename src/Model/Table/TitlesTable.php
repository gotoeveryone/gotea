<?php

namespace App\Model\Table;

use Cake\Validation\Validator;
use Cake\ORM\Query;
use Cake\ORM\ResultSet;
use App\Model\Entity\Title;

/**
 * タイトル
 */
class TitlesTable extends AppTable
{
    /**
     * {@inheritdoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

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
     * {@inheritdoc}
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
        if (($countryId = $data['country_id'] ?? '')) {
            $query->where(['Countries.id' => $countryId]);
        }

        // 有効なタイトルのみ検索
        if (!($data['is_closed'] ?? '')) {
            $query->where(['Titles.is_closed' => 0]);
        }

        // データを取得
        return $query->order(['Titles.country_id', 'Titles.sort_order'])->all();
    }

    /**
     * モデルを配列に変換します。
     *
     * @param ResultSet $models
     * @param bool $admin 管理者情報を取得するか
     * @param bool $withJa 日本語情報を取得するか
     * @return array
     */
    public function toArray(ResultSet $models, $admin = false, $withJa = false) : array
    {
        return $models->map(function(Title $item, $key) use ($admin, $withJa) {
            $data = [
                'countryName' => $item->country->name_english,
                'countryNameAbbreviation' => $item->country->code,
                'titleName' => $item->name_english,
                'holding' => $item->holding,
                'isTeam' => $item->is_team,
                'winnerName' => $item->getWinnerName($withJa),
                'htmlFileName' => $item->html_file_name,
                'htmlFileModified' => $item->html_file_modified->format(($admin ? 'Y/m/d' : 'Y-m-d')),
                'isNewHistories' => $item->isNewHistories(),
                'isRecent' => $item->isRecentModified(),
            ];

            // 日本語情報出力あり
            if ($withJa) {
                $data['titleId'] = $item->id;
                $data['countryId'] = $item->country_id;
                $data['sortOrder'] = $item->sort_order;
                $data['titleNameJp'] = $item->name;
                $data['isClosed'] = $item->is_closed;
            }

            return $data;
        })->toArray();
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
