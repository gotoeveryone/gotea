<?php
namespace App\Controller;

use App\Form\TitleScoreForm;

/**
 * TitleScores Controller
 *
 * @property \App\Model\Table\TitleScoresTable $TitleScores
 * @property \App\Model\Table\TitleScoreDetailsTable $TitleScoreDetails
 */
class TitleScoresController extends AppController
{
    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadModel('TitleScoreDetails');
    }

    /**
     * 初期表示、検索処理
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->set('form', ($form = new TitleScoreForm));

        // 初期表示
        if (!$this->request->isPost()) {
            return $this->_renderWith('タイトル勝敗検索', 'index');
        }

        // バリデーション
        if (!$form->validate($this->request->getParsedBody())) {
            return $this->_renderWithErrors($form->errors(), 'タイトル勝敗検索', 'index');
        }

        // モーダル表示かどうか
        if ($this->request->getData('modal')) {
            $this->_enableDialogMode();
        }

        // リクエストから値を取得
        $data = $this->request->getParsedBody();
        $titleScores = $this->TitleScores->findMatches($data);

        // 件数が0件または多すぎる場合はメッセージを出力
        $over = 500;
        if (!$titleScores->count()) {
            $this->Flash->warn(__("検索結果が0件でした。"));
        } elseif (($count = $titleScores->count()) > $over) {
            $warning = '検索結果が{0}件を超えています（{1}件）。<br/>条件を絞って再検索してください。';
            $this->Flash->warn(__($warning, $over, $count));
        } else {
            // 結果をセット
            $titleScores = $this->TitleScores->findMatches($data);
            $this->set(compact('titleScores'));
        }

        return $this->_renderWith('タイトル勝敗検索', 'index');
    }

    /**
     * 勝敗変更処理
     *
     * @return \Cake\Http\Response|null
     */
    public function change()
    {
        // POST以外は許可しない
        $this->request->allowMethod(['post']);

        $changeId = $this->request->getData('change_id');
        $model = $this->TitleScores->findById($changeId)->contain(['TitleScoreDetails'])->first();
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
            $this->Flash->info("ID【{$changeId}】の勝敗を変更しました。");
        }

        return $this->index();
    }

    /**
     * 削除処理
     *
     * @return \Cake\Http\Response|null
     */
    public function delete()
    {
        // POST以外は許可しない
        $this->request->allowMethod(['post']);

        $deleteId = $this->request->getData('delete_id');
        $model = $this->TitleScores->findById($deleteId)->contain(['TitleScoreDetails'])->first();

        foreach ($model->title_score_details as $detail) {
            $this->TitleScoreDetails->delete($detail);
        }
        $this->TitleScores->delete($model);

        $this->Flash->info("ID【{$deleteId}】の成績情報を削除しました。");
        return $this->index();
    }
}
