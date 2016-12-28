<?php

namespace App\Controller;

use Cake\Event\Event;

/**
 * 成績更新日編集用コントローラ
 *
 * @author  Kazuki Kamizuru
 * @since   2015/08/15
 * 
 * @property \App\Model\Table\UpdatedPointsTable $UpdatedPoints
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
        // 成績更新日情報の取得
        $updatedPoints = $this->UpdatedPoints->findScoreUpdateHasYear($this->request->data('year'));
        $this->set('updatedPoints', $updatedPoints);

        // 初期処理
        return $this->setAction('index');
    }

    /**
     * 登録・更新処理
     */
	public function save() {
        // POSTされたタイトル情報から、登録 or 更新対象の一覧を生成
        $rows = $this->request->data('results');

        // 更新対象が取得できなければ、検索結果表示処理へ
        if (!($targets = $this->__createUpdateTargets($rows))) {
            // TODO: この場合再検索になるため入力値が消えるが、ビューにオブジェクトの一覧を返せない為止むを得ない
			return $this->setAction('search');
        }

        // 件数分処理
        foreach ($targets as $target) {
            // タイトル情報を更新
            $this->UpdatedPoints->save($target);
        }
        $this->Flash->info(__(count($targets).'件の成績更新日情報を更新しました。'));

        // indexの処理を行う
        return $this->search();
	}

    /**
     * 登録・更新対象の一覧を生成します。
     * 
     * @param array $rows
     * @return array
     */
    private function __createUpdateTargets(array $rows)
    {
        $targets = [];
        foreach ($rows as $row) {
            // 更新対象でなければ処理しない
            if (!$row['update_flag']) {
                continue;
            }

            // バリデーションエラーの場合はそのまま返す
            if (($errors = $this->UpdatedPoints->validator()->errors($row))) {
                // エラーメッセージを書き込み
                $this->Flash->error($errors);
                return null;
            }

            // データを取得し、POSTされた値を設定
            $title = $this->UpdatedPoints->get($row['id']);
            $this->UpdatedPoints->patchEntity($title, $row);

            // 一覧に追加
            array_push($targets, $title);
        }
        return $targets;
    }
}
