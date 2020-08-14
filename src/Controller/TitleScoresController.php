<?php
declare(strict_types=1);

namespace Gotea\Controller;

use Cake\Http\Response;
use Gotea\Form\TitleScoreForm;

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
    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadModel('Players');
        $this->loadModel('Titles');
        $this->loadModel('TitleScoreDetails');

        $this->loadComponent('Paginator');
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
            return $this->setErrors(400, $form->getErrors())->setAction('index');
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
     * @param string $id 棋士ID
     * @param string $year 対象年度
     * @return \Cake\Http\Response|null
     */
    public function searchByPlayer(string $id, string $year): ?Response
    {
        $id = (int)$id;
        $year = (int)$year;
        $player = $this->Players->get($id);
        $titleScores = $this->TitleScores->findMatches([
            'player_id' => $id,
            'target_year' => $year,
        ]);
        $detail = $this->TitleScoreDetails->findByPlayerAtYear($id, $year);
        $this->set(compact('player', 'year', 'titleScores', 'detail'));

        return $this->renderWithDialog('player');
    }

    /**
     * 詳細表示処理
     *
     * @param string $id 取得するデータのID
     * @return \Cake\Http\Response|null
     */
    public function view(string $id): ?Response
    {
        $score = $this->TitleScores->findByIdWithRelation((int)$id);
        $activeTitles = $this->Titles->findSortedList();

        return $this->set(compact('score', 'activeTitles'))->renderWithDialog();
    }

    /**
     * 更新処理
     *
     * @param string $id 成績ID
     * @return \Cake\Http\Response|null
     */
    public function update(string $id): ?Response
    {
        $id = (int)$id;

        // 勝敗変更の場合は該当アクションを実施
        if ($this->getRequest()->getData('action') == 'switchDivision') {
            return $this->switchDivision($id);
        }

        // データ取得
        $score = $this->TitleScores->findByIdWithRelation($id);
        $this->TitleScores->patchEntity($score, $this->getRequest()->getParsedBody());

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
     * @param string $id 成績ID
     * @return \Cake\Http\Response|null
     */
    public function delete(string $id): ?Response
    {
        $model = $this->TitleScores->get($id, [
            'contain' => 'TitleScoreDetails',
        ]);

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
    private function switchDivision(string $id): ?Response
    {
        $score = $this->TitleScores->get($id, [
            'contain' => 'TitleScoreDetails',
        ]);

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
