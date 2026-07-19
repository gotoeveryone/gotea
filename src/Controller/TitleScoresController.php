<?php
declare(strict_types=1);

namespace Gotea\Controller;

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\Log\Log;
use Gotea\Form\TitleScoreForm;
use Gotea\Model\Table\PlayersTable;
use Gotea\Model\Table\TitleScoreDetailsTable;
use Gotea\Model\Table\TitlesTable;
use SplFileObject;

/**
 * TitleScores Controller
 *
 * @property \Gotea\Model\Table\TitleScoresTable $TitleScores
 * @property \Gotea\Model\Table\PlayersTable $Players
 * @property \Gotea\Model\Table\TitlesTable $Titles
 * @property \Gotea\Model\Table\TitleScoreDetailsTable $TitleScoreDetails
 */
class TitleScoresController extends AppController
{
    protected PlayersTable $Players;
    protected TitlesTable $Titles;
    protected TitleScoreDetailsTable $TitleScoreDetails;

    /**
     * @inheritDoc
     */
    public function beforeFilter(EventInterface $event)
    {
        $this->Authorization->authorize($this->request, 'access');

        parent::beforeFilter($event);
    }

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->Players = $this->fetchTable('Players');
        $this->Titles = $this->fetchTable('Titles');
        $this->TitleScoreDetails = $this->fetchTable('TitleScoreDetails');
    }

    /**
     * 初期表示処理
     *
     * @return \Cake\Http\Response|null
     */
    public function index(): ?Response
    {
        $this->set('form', new TitleScoreForm());

        return $this->renderWith('タイトル勝敗検索', 'index');
    }

    /**
     * 検索処理
     *
     * @return \Cake\Http\Response|null
     */
    public function search(): ?Response
    {
        $this->set('form', ($form = new TitleScoreForm()));

        // バリデーション
        $data = $this->getRequest()->getQueryParams();
        if (!$form->validate($data)) {
            return $this->setErrors(400, $form->getErrors())->render('index');
        }

        // リクエストから値を取得
        $titleScores = $this->paginate($this->TitleScores->findMatches($data));

        // 件数が0件の場合はメッセージを出力
        if (!$titleScores->count()) {
            $this->Flash->warn(__('No matches found'));
        } else {
            // 結果をセット
            $this->set(compact('titleScores'));
        }

        return $this->renderWith('タイトル勝敗検索', 'index');
    }

    /**
     * 指定した棋士・年度に該当する成績の取得処理
     *
     * @param int $id 棋士ID
     * @param int $year 対象年度
     * @return \Cake\Http\Response|null
     */
    public function searchByPlayer(int $id, int $year): ?Response
    {
        $player = $this->Players->get($id, contain: ['Ranks']);
        $titleScores = $this->TitleScores->findMatches([
            'player_id' => $id,
            'target_year' => $year,
        ]);
        $detail = $this->TitleScoreDetails->findByPlayerAtYear($id, $year);
        $this->set(compact('player', 'year', 'titleScores', 'detail'));

        return $this->renderWithDialog('player');
    }

    /**
     * タイトル成績アップロード処理
     *
     * @return \Cake\Http\Response|null
     */
    public function upload(): ?Response
    {
        if ($this->request->getMethod() === 'GET') {
            return $this->renderWithDialog();
        }

        // ファイル生成
        $file = $this->request->getUploadedFile('file');
        if (!$file || !in_array($file->getClientMediaType(), ['text/csv', 'application/vnd.ms-excel'], true)) {
            return $this->renderWithDialogErrors(400, __('Upload file was not csv'));
        }

        $csv = new SplFileObject($file->getStream()->getMetadata('uri'));
        $csv->setFlags(
            SplFileObject::READ_CSV |
            SplFileObject::SKIP_EMPTY |
            SplFileObject::DROP_NEW_LINE |
            SplFileObject::READ_AHEAD,
        );

        $rows = [];
        $playerIds = [];
        foreach ($csv as $i => $row) {
            // 1行目は無視
            if ($i === 0) {
                continue;
            }
            // 列数を満たしていない場合はエラー
            if (count($row) < 13) {
                return $this->renderWithDialogErrors(400, __(
                    'Column was insufficient, need: {0}, actual: {1}',
                    13,
                    count($row),
                ));
            }
            [
                $countryId,
                $started,
                $ended,
                $name,
                $result,
                $isWorld,
                $isOfficial,
                $player1Id,
                // phpcs:ignore
                $player1Name, // TODO: 今後（アマチュアなど）登録されていない棋士の情報も保存したいので、現状使っていないがカラムとしては用意しておく
                $player1Division,
                $player2Id,
                // phpcs:ignore
                $player2Name,
                $player2Division,
            ] = $row;
            $rows[] = [
                'country_id' => $countryId,
                'name' => $name,
                'result' => $result,
                'started' => str_replace('/', '-', $started),
                'ended' => str_replace('/', '-', $ended),
                'is_world' => $isWorld,
                'is_official' => $isOfficial,
                'player1_id' => $player1Id,
                'player1_division' => $player1Division,
                'player2_id' => $player2Id,
                'player2_division' => $player2Division,
            ];
            if ($player1Id) {
                $playerIds[] = $player1Id;
            }
            if ($player2Id) {
                $playerIds[] = $player2Id;
            }
        }

        if (!$rows) {
            return $this->renderWithDialogErrors(400, __('Data not exist'));
        }

        // CSVに含まれる棋士を一括取得し、行処理ではDBへ問い合わせない
        $players = [];
        if ($playerIds) {
            foreach (
                $this->Players->find()
                    ->whereInList('Players.id', array_unique($playerIds))
                    ->all() as $player
            ) {
                $players[$player->id] = $player;
            }
        }

        $data = [];
        foreach ($rows as $row) {
            [
                'country_id' => $countryId,
                'name' => $name,
                'result' => $result,
                'started' => $started,
                'ended' => $ended,
                'is_world' => $isWorld,
                'is_official' => $isOfficial,
                'player1_id' => $player1Id,
                'player1_division' => $player1Division,
                'player2_id' => $player2Id,
                'player2_division' => $player2Division,
            ] = $row;

            $item = [
                'country_id' => $countryId,
                'name' => $name,
                'result' => $result,
                'started' => $started,
                'ended' => $ended,
                'is_world' => $isWorld,
                'is_official' => $isOfficial,
                'title_score_details' => [],
            ];
            try {
                foreach (
                    [
                        [$player1Id, $player1Division],
                        [$player2Id, $player2Division],
                    ] as [$playerId, $division]
                ) {
                    if (!$playerId) {
                        continue;
                    }
                    if (!isset($players[$playerId])) {
                        throw new RecordNotFoundException(sprintf(
                            'Record not found in table `%s`.',
                            $this->Players->getTable(),
                        ));
                    }
                    $item['title_score_details'][] = [
                        'player_id' => $playerId,
                        'player_name' => $players[$playerId]->name,
                        'division' => $division,
                    ];
                }
            } catch (RecordNotFoundException $e) {
                Log::error($e->getMessage());

                return $this->renderWithDialogErrors(400, __('Data not exist'));
            }
            $data[] = $item;
        }

        $entities = $this->TitleScores->newEntities($data);
        if (!$this->TitleScores->saveMany($entities)) {
            return $this->renderWithDialogErrors(400, __('Has errors in uploaded data'));
        }

        $this->setMessages(__('Uploaded {0} rows', count($entities)));

        return $this->redirect(['_name' => 'upload_scores']);
    }

    /**
     * 詳細表示処理
     *
     * @param int $id 取得するデータのID
     * @return \Cake\Http\Response|null
     */
    public function view(int $id): ?Response
    {
        $score = $this->TitleScores->findByIdWithRelation($id);
        $activeTitles = $this->Titles->findSortedList();

        return $this->set(compact('score', 'activeTitles'))->renderWithDialog();
    }

    /**
     * 更新処理
     *
     * @param int $id 成績ID
     * @return \Cake\Http\Response|null
     */
    public function update(int $id): ?Response
    {
        // 勝敗変更の場合は該当アクションを実施
        if ($this->getRequest()->getData('action') == 'switchDivision') {
            return $this->switchDivision($id);
        }

        // 開始日と同じにチェックがあった場合、終了日には開始日と同じ値をセットする
        $data = $this->request->getData();
        if ($this->request->getData('is_same_started')) {
            $data['ended'] = $data['started'];
        }

        // データ取得
        $score = $this->TitleScores->findByIdWithRelation($id);
        $this->TitleScores->patchEntity($score, $data);

        // 保存
        if (!$this->TitleScores->save($score)) {
            $activeTitles = $this->Titles->findSortedList();
            $this->set(compact('score', 'activeTitles'));

            return $this->renderWithDialogErrors(400, $score->getErrors(), 'view');
        } else {
            $this->setMessages(__('The score {0} - {1} is saved', $score->id, $score->name));
        }

        return $this->redirect([
            '_name' => 'view_score',
            $score->id,
        ]);
    }

    /**
     * 削除処理
     *
     * @param int $id 成績ID
     * @return \Cake\Http\Response|null
     */
    public function delete(int $id): ?Response
    {
        $model = $this->TitleScores->get($id, contain: ['TitleScoreDetails']);

        foreach ($model->title_score_details as $detail) {
            $this->TitleScoreDetails->delete($detail);
        }
        $this->TitleScores->delete($model);

        $this->setMessages("ID【{$id}】の成績情報を削除しました。");

        return $this->redirect([
            '_name' => 'find_scores',
            '?' => $this->getRequest()->getParsedBody(),
        ]);
    }

    /**
     * 勝敗変更処理
     *
     * @param int $id 成績ID
     * @return \Cake\Http\Response|null
     */
    private function switchDivision(int $id): ?Response
    {
        $score = $this->TitleScores->get($id, contain: ['TitleScoreDetails']);

        $changed = 0;
        foreach ($score->title_score_details as $detail) {
            switch ($detail->division) {
                case '勝':
                case '敗':
                    $detail->division = ($detail->division === '勝' ? '敗' : '勝');
                    $this->TitleScoreDetails->save($detail);
                    $changed++;
                    break;
                default:
                    break;
            }
        }

        if ($changed === 2) {
            $this->setMessages("ID【{$id}】の勝敗を変更しました。");
        }

        return $this->redirect([
            '_name' => 'view_score',
            $score->id,
        ]);
    }
}
