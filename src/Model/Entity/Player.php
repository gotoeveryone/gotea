<?php

namespace Gotea\Model\Entity;

use Cake\I18n\FrozenDate;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

/**
 * 棋士エンティティ
 *
 * @property int $id
 * @property int $country_id
 * @property int $rank_id
 * @property int $organization_id
 * @property string $name
 * @property string $name_english
 * @property string $name_other
 * @property string $sex
 * @property string $joined
 * @property \Cake\I18n\FrozenDate $birthday
 * @property string $remarks
 * @property bool $is_retired
 * @property \Cake\I18n\FrozenDate $retired
 * @property \Cake\I18n\FrozenTime $created
 * @property string $created_by
 * @property \Cake\I18n\FrozenTime $modified
 * @property string $updated_by
 *
 * @property \Gotea\Model\Entity\Country $country
 * @property \Gotea\Model\Entity\Rank $rank
 * @property \Gotea\Model\Entity\Organization $organization
 *
 * @property array $input_joined
 * @property array $format_joined
 * @property int|null $age
 * @property string $name_with_country
 * @property string $name_with_rank
 * @property \Cake\Collection\CollectionInterface $old_scores
 * @property \Cake\Collection\CollectionInterface $retention_histories
 */
class Player extends AppEntity
{
    use CountryTrait;
    use RankTrait;

    /**
     * 入力フォーム用の入段日を取得します。
     *
     * @return array 入力フォーム用の入段日
     */
    protected function _getInputJoined()
    {
        $value = $this->joined;
        if (is_array($value)) {
            return $value;
        }

        $res = [];
        if (strlen($value) >= 4) {
            $res['year'] = substr($value, 0, 4);
        }
        if (strlen($value) >= 6) {
            $res['month'] = substr($value, 4, 2);
        }
        if (strlen($value) >= 8) {
            $res['day'] = substr($value, 6, 2);
        }

        return $res;
    }

    /**
     * 入段日を日付フォーマットに変更して取得します。
     *
     * @return array 入段日
     */
    protected function _getFormatJoined()
    {
        $joined = collection($this->input_joined)->reject(function ($item) {
            return $item === '' || $item === null;
        })->toArray();

        return implode('/', $joined);
    }

    /**
     * 年齢を取得します。
     *
     * @return int|null 年齢
     */
    protected function _getAge()
    {
        return $this->birthday ? $this->birthday->age : null;
    }

    /**
     * 棋士名と所属国を取得します。
     *
     * @return string 棋士名 (所属国)
     */
    protected function _getNameWithCountry()
    {
        return "{$this->name} ({$this->country->name})";
    }

    /**
     * 棋士名と段位を取得します。
     *
     * @return string 棋士名 段位
     */
    protected function _getNameWithRank()
    {
        return "{$this->name} {$this->rank->name}";
    }

    /**
     * 棋士の成績（旧取得方式）を取得します。
     *
     * @param mixed $value 設定値
     * @return \Cake\Collection\CollectionInterface 昇段情報
     */
    protected function _getOldScores($value)
    {
        if ($value) {
            return $value;
        }

        if (!$this->id) {
            return collection([]);
        }

        $result = TableRegistry::getTableLocator()->get('PlayerScores')->findDescYears($this->id);

        return $this->old_scores = $result;
    }

    /**
     * 棋士のタイトル獲得履歴を取得します。
     *
     * @param mixed $value 設定値
     * @return \Cake\Collection\CollectionInterface タイトル獲得履歴
     */
    protected function _getRetentionHistories($value)
    {
        if ($value) {
            return $value;
        }

        if (!$this->id) {
            return collection([]);
        }

        $result = TableRegistry::getTableLocator()->get('RetentionHistories')->findHistoriesByPlayer($this->id);

        return $this->retention_histories = $result;
    }

    /**
     * 誕生日を設定します。
     *
     * @param mixed $birthday 設定値
     * @return \Cake\I18n\FrozenDate
     */
    protected function _setBirthday($birthday)
    {
        if ($birthday && !($birthday instanceof FrozenDate)) {
            return FrozenDate::parseDate($birthday, 'yyyy/MM/dd');
        }

        return $birthday;
    }

    /**
     * 入段日を設定します。
     *
     * @param mixed $joined 設定値
     * @return string
     */
    protected function _setInputJoined($joined)
    {
        if (!is_array($joined)) {
            return $this->joined = $joined;
        }

        $value = '';
        if (isset($joined['year'])) {
            $value .= sprintf('%04d', $joined['year']);
        }
        if (!empty($joined['month'])) {
            $value .= sprintf('%02d', $joined['month']);
        }
        if (!empty($joined['day']) && strlen($value) === 6) {
            $value .= sprintf('%02d', $joined['day']);
        }

        return $this->joined = $value;
    }

    /**
     * 保存先のURLを取得します。
     *
     * @return string URL
     */
    public function getSaveUrl()
    {
        if ($this->isNew()) {
            return Router::url(['_name' => 'create_player']);
        }

        return Router::url(['_name' => 'update_player', $this->id]);
    }

    /**
     * 年度単位でグループ化します。
     *
     * @return Cake\Collection\Collection
     */
    public function groupByYearFromHistories()
    {
        return $this->retention_histories->groupBy('target_year');
    }

    /**
     * モデルのデータを出力用配列形式で返却します。
     *
     * @return array 配列
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'nameEnglish' => $this->name_english,
            'nameOther' => $this->name_other,
            'sex' => $this->sex,
            'birthday' => $this->birthday ? $this->birthday->format('Y/m/d') : null,
            'countryName' => $this->country->name,
            'rankId' => $this->rank->id,
            'rankName' => $this->rank->name,
            'isRetired' => $this->is_retired,
            'retired' => $this->retired,
        ];
    }

    /**
     * 男性かどうかを判定します。
     *
     * @return bool 男性ならtrue
     */
    public function isMale(): bool
    {
        return $this->sex === '男性';
    }

    /**
     * 女性かどうかを判定します。
     *
     * @return bool 女性ならtrue
     */
    public function isFemale(): bool
    {
        return $this->sex === '女性';
    }

    /**
     * 勝数を取得します。
     *
     * @param int|null $year 対象年度
     * @param bool $world 対象が国際棋戦かどうか
     * @return int|string 勝数
     */
    public function win($year = null, $world = false)
    {
        return $this->show('win', $year, $world);
    }

    /**
     * 敗数を取得します。
     *
     * @param int|null $year 対象年度
     * @param bool $world 対象が国際棋戦かどうか
     * @return int|string 敗数
     */
    public function lose($year = null, $world = false)
    {
        return $this->show('lose', $year, $world);
    }

    /**
     * 引分数を取得します。
     *
     * @param int|null $year 対象年度
     * @param bool $world 対象が国際棋戦かどうか
     * @return int|string 引分数
     */
    public function draw($year = null, $world = false)
    {
        return $this->show('draw', $year, $world);
    }

    /**
     * 指定された成績の値を取得します。
     *
     * @param string $type 取得する成績の分類
     * @param int|null $year 対象年度
     * @param bool $world 対象が国際棋戦かどうか
     * @return int|string 対象数
     */
    private function show($type, $year = null, $world = false)
    {
        if ($year === null) {
            $year = FrozenDate::now()->year;
        }
        $scores = collection($this->title_score_details);
        $score = $scores->filter(function ($item) use ($year) {
            return (int)$item->player_id === $this->id
                && (int)$item->target_year === $year;
        })->first();

        // 該当年の対局がない
        if (!$score) {
            // 前年以前に引退している場合は'-'固定
            if ($this->is_retired && (!$this->retired || $year > $this->retired->year)) {
                return '-';
            }

            return 0;
        }

        $propertyName = ($world) ? "${type}_point_world" : "${type}_point";

        return (int)$score->$propertyName;
    }
}
