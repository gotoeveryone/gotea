<?php

namespace App\Controller;

use PDOException;
use Cake\Event\Event;
use Psr\Log\LogLevel;

/**
 * 成績更新日編集用コントローラ
 *
 * @author		Kazuki Kamizuru
 * @since		2015/08/15
 */
class ScoreUpdatesController extends AppController
{
	/**
	 * 描画前処理
	 */
    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);

        $this->set('cakeDescription', '成績更新日編集');
		// 所属国プルダウン
		$this->set('years', $this->ScoreUpdates->find('list', [
            'keyField' => 'keyField',
            'valueField' => 'valueField'
        ])->group(['ScoreUpdates.TARGET_YEAR'])->order(['ScoreUpdates.TARGET_YEAR' => 'DESC'])->select([
            'keyField' => 'ScoreUpdates.TARGET_YEAR',
            'valueField' => 'ScoreUpdates.TARGET_YEAR'
        ])->toArray());
    }

	/**
	 * 初期処理
	 */
	public function index()
    {
        return $this->render('index');
    }

    /**
	 * 検索処理
	 */
	public function search()
    {
        // リクエストから値を取得（なければセッションから取得）
        $searchYear = $this->_getParam('searchYear');
//        var_dump($this->request->session()->read('Auth'));

        // 成績更新日情報の取得
        $scoreUpdates = $this->ScoreUpdates->find()->where([
            'ScoreUpdates.TARGET_YEAR' => $searchYear
        ])->order([
            'ScoreUpdates.COUNTRY_CD',
            'ScoreUpdates.TARGET_YEAR' => 'DESC'
        ])->contain(['Countries'])->all();

        $this->set('scoreUpdates', $scoreUpdates);

        // 検索フラグを設定
		$this->set('searchFlag', true);

        // 値を格納
        $this->_setParam('searchYear', $searchYear);

        // 初期処理
        return $this->index();
    }

    /**
     * 登録・更新処理
     */
	public function save() {
        // POSTされたタイトル情報を取得
        $rows = $this->request->data('scoreUpdates');

        // 更新件数
        $count = 0;

        // 登録 or 更新対象の一覧を生成
        $targets = [];
        foreach ($rows as $row) {
            // 更新対象でなければ処理しない
            if ($row['updateFlag'] !== 'true') {
                continue;
            }

            // データを取得し、POSTされた値を設定
            $title = $this->ScoreUpdates->get($row['scoreId']);
            $title->set('SCORE_UPDATE_DATE', $row['scoreUpdateDate']);

            // バリデーションエラーの場合はそのまま返す
            $validator = $this->ScoreUpdates->validator('default');
            $res = $validator->errors($title->toArray());
            if ($res) {
                // エラーメッセージを書き込み
                $error = $this->_getErrorMessage($res);
                $this->Flash->error($error);
                // 検索結果表示処理へ
                // TODO: この場合再検索になるため入力値が消えるが、ビューにオブジェクトの一覧を返せない為止むを得ない
                return $this->search();
            }
            // 一覧に追加
            array_push($targets, $title);
        }

        try {
			// 件数分処理
			foreach ($targets as $target) {
                // タイトル情報を更新
                $this->ScoreUpdates->save($target);
                $count++;
			}
			$this->Flash->info($count.'件の成績更新日情報を更新しました。');
		} catch (PDOException $e) {
			$this->log('成績更新日情報更新エラー：'.$e->getMessage(), LogLevel::ERROR);
			$this->isrollback = true;
			$this->Flash->error('成績更新日情報の更新に失敗しました…。');
		} finally {
			// indexの処理を行う
			return $this->search();
		}
	}
}
