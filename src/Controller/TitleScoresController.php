<?php
namespace App\Controller;

use Cake\Event\Event;
use App\Form\TitleScoreForm;

/**
 * TitleScores Controller
 *
 * @property \App\Model\Table\TitleScoresTable $TitleScores
 */
class TitleScoresController extends AppController
{
    /**
     * 初期処理
     */
	public function initialize()
    {
        parent::initialize();

        $this->_setTitle('タイトル勝敗検索');

        // モデルをロード
        $this->loadModel('TitleScoreDetails');
    }

    /**
     * 初期表示、検索処理
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        // ダイアログ状態でない場合はヘッダを取得
        if (!$this->_isDialogMode()) {
            // 所属国プルダウン
            $this->loadModel('Countries');
            $this->set('countries', $this->Countries->findToKeyValue());

            // 年度プルダウン
            $years = [];
            for ($i = date('Y'); $i >= 2013; $i--) {
                $years[$i] = $i.'年度';
            }
            $this->set('years', $years);
        }

        // 検索
        if ($this->request->isPost()) {
            $form = new TitleScoreForm();
            if (!$form->validate($this->request->getParsedBody())) {
                $this->Flash->error($form->errors());
                return $this->set('form', $form)->render('index');
            }

            // リクエストから値を取得
            $data = $this->request->getParsedBody();
            $count = $this->TitleScores->findMatches($data, true);

            if ($count === 0) {
                $this->Flash->warn(__("検索結果が0件でした。"));
            } else if ($count > 500) {
                $this->Flash->warn(__("検索結果が500件を超えています（{$count}件）。<BR>条件を絞って再検索してください。"));
            } else {
                // 結果をセット
                $titleScores = $this->TitleScores->findMatches($data);
                $this->set('titleScores', $titleScores);
            }
        }

        return $this->set('form', ($form ?? new TitleScoreForm))->render('index');
    }

    /**
     * 詳細画面からの検索処理
     *
     * @return \Cake\Network\Response|null
     */
    public function modalSearch()
    {
        return $this->_setDialogMode()->index();
    }

    /**
     * 勝敗変更処理
     *
     * @return \Cake\Network\Response|null
     */
    public function change()
    {
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
     * @return \Cake\Network\Response|null
     */
    public function delete()
    {
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
