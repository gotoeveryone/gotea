<?php
namespace Gotea\Controller;

use Gotea\Form\TitleScoreForm;

/**
 * TitleScores Controller
 *
 * @property \Gotea\Model\Table\TitleScoresTable $TitleScores
 * @property \Gotea\Model\Table\TitleScoreDetailsTable $TitleScoreDetails
 */
class TitleScoresController extends AppController
{
    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadModel('Players');
        $this->loadModel('TitleScoreDetails');
    }

    /**
     * 初期表示処理
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->set('form', new TitleScoreForm);

        return $this->renderWith('タイトル勝敗検索', 'index');
    }

    /**
     * 検索処理
     *
     * @return \Cake\Http\Response|null
     */
    public function search()
    {
        $this->set('form', ($form = new TitleScoreForm));

        // バリデーション
        if (!$form->validate($this->request->getParsedBody())) {
            return $this->setErrors(400, $form->errors())->setAction('index');
        }

        // リクエストから値を取得
        $data = $this->request->getParsedBody();
        $titleScores = $this->TitleScores->findMatches($data);

        // 件数が0件または多すぎる場合はメッセージを出力
        $over = 300;
        if (!($count = $titleScores->count())) {
            $this->Flash->warn(__('No matches found'));
        } elseif ($count > $over) {
            $this->Flash->warn(__(
                'Matched rows more than {0} ({1} row matched).<br/>Please filtering conditions and reexecute.',
                $over,
                $count
            ));
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
    public function searchByPlayer(int $id, int $year)
    {
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
     * 更新処理
     *
     * @param int $id 成績ID
     * @return \Cake\Http\Response|null
     */
    public function update(int $id)
    {
        $model = $this->TitleScores->get($id, [
            'contain' => 'TitleScoreDetails',
        ]);

        $changed = 0;
        foreach ($model->title_score_details as $detail) {
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
            $this->Flash->info("ID【{$id}】の勝敗を変更しました。");
        }

        return $this->setAction('search');
    }

    /**
     * 削除処理
     *
     * @param int $id 成績ID
     * @return \Cake\Http\Response|null
     */
    public function delete(int $id)
    {
        $model = $this->TitleScores->get($id, [
            'contain' => 'TitleScoreDetails',
        ]);

        foreach ($model->title_score_details as $detail) {
            $this->TitleScoreDetails->delete($detail);
        }
        $this->TitleScores->delete($model);

        $this->Flash->info("ID【{$id}】の成績情報を削除しました。");

        return $this->setAction('search');
    }
}
