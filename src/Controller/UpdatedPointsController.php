<?php

namespace App\Controller;

use PDOException;
use Cake\Event\Event;

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

        // バリデーションにひっかかった場合、一覧を復元
        if (!($targets = $this->__createUpdateTargets($rows))) {
//            $data = [];
//            foreach ($rows as $key => $row) {
//                $p = $this->UpdatedPoints->newEntity(['id' => $row['id']]);
//                $dest = $this->UpdatedPoints->patchEntity($p, $row, ['validate' => false]);
//                $a = $dest->modified->i18nFormat('YYYY/mm/dd HH:mm:ss');
//                if ($row['update_flag']) {
//                    $dest->update_flag = $row['update_flag'];
//                }
//                $data[] = $dest;
//            }
//			// indexの処理を行う
//            $this->set('updatedPoints', $data);
			return $this->setAction('search');
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
			$this->Flash->info(__("{$count}件の成績更新日情報を更新しました。"));
		} catch (PDOException $e) {
			$this->Log->error(__("成績更新日情報更新エラー：{$e->getMessage()}"));
			$this->Flash->error(__("成績更新日情報の更新に失敗しました…。"));
			$this->_markToRollback();
		} finally {
			// indexの処理を行う
			return $this->search();
		}
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

            // データを取得し、POSTされた値を設定
            $title = $this->UpdatedPoints->get($row['id']);
            $this->UpdatedPoints->patchEntity($title, $row, ['validate' => false]);

            // バリデーションエラーの場合はそのまま返す
            if (($errors = $this->UpdatedPoints->validator()->errors($title->toArray()))) {
                // エラーメッセージを書き込み
                $this->Flash->error($errors);
                return null;
            }
            // 一覧に追加
            array_push($targets, $title);
        }
        return $targets;
    }
}
