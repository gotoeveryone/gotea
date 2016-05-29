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
class UpdatedPointsController extends AppController
{
	/**
	 * 描画前処理
	 */
    public function beforeRender(Event $event)
    {
        $this->_setTitle('成績更新日編集');
        parent::beforeRender($event);
		// 所属国プルダウン
		$this->set('years', $this->UpdatedPoints->findToArray());
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

        // 成績更新日情報の取得
        $updatedPoints = $this->UpdatedPoints->findScoreUpdateHasYear($searchYear);

        $this->set('updatedPoints', $updatedPoints);

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

        // 登録 or 更新対象の一覧を生成
        $targets = [];
        foreach ($rows as $row) {
            // 更新対象でなければ処理しない
            if ($row['updateFlag'] !== 'true') {
                continue;
            }

            // データを取得し、POSTされた値を設定
            $title = $this->UpdatedPoints->get($row['scoreId']);
            $title->score_updated = $row['scoreUpdateDate'];

            // バリデーションエラーの場合はそのまま返す
            $res = $this->UpdatedPoints->validator()->errors($title->toArray());
            if ($res) {
                // エラーメッセージを書き込み
                $this->Flash->error(__($this->_getErrorMessage($res)));
                // 検索結果表示処理へ
                // TODO: この場合再検索になるため入力値が消えるが、ビューにオブジェクトの一覧を返せない為止むを得ない
                return $this->search();
            }
            // 一覧に追加
            array_push($targets, $title);
        }

        try {
            // 更新件数
            $count = 0;

			// 件数分処理
			foreach ($targets as $target) {
                // タイトル情報を更新
                $this->UpdatedPoints->save($target);
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
