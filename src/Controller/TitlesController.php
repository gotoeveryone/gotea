<?php

namespace App\Controller;

use Cake\Http\Response;
use Cake\Network\Exception\NotFoundException;

/**
 * タイトルマスタ用コントローラ
 *
 * @author		Kazuki Kamizuru
 * @since		2015/07/25
 *
 * @property \App\Model\Table\TitlesTable $Titles
 * @property \App\Model\Table\RetentionHistoriesTable $RetentionHistories
 */
class TitlesController extends AppController
{
	/**
	 * 初期処理
     *
     * @return Response
	 */
	public function index()
    {
        return $this->_setTitle('タイトル情報検索')->render();
    }

	/**
	 * 詳細情報表示・更新処理
     *
     * @param int|null $id 取得するデータのID
     * @param bool $save 保存処理をおこなうかどうか
     * @return Response
	 */
	public function detail($id = null, $save = true)
    {
        // ダイアログ表示
        $this->_setDialogMode();

        // 保存処理
        if ($id !== null && $save && $this->request->isPost()) {
            // バリデーションエラーの場合は処理終了
            $data = $this->request->getParsedBody();
            if (($errors = $this->Titles->validator()->errors($data))) {
                $this->Flash->error($errors);
                return $this->render('detail');
            }

            // 保存処理
            if (($title = $this->Titles->saveEntity($data))) {
                $this->Flash->info(__("タイトル：{$title->name}を更新しました。"));
            }
        } elseif (!($title = $this->Titles->findWithRelations($id))) {
            throw new NotFoundException(__("タイトル情報が取得できませんでした。ID：{$id}"));
        }

        $this->set('title', $title);

        return $this->render('detail');
    }

	/**
	 * タイトル保持情報の登録
     *
     * @return Response
	 */
	public function addHistory()
    {
        // POST以外は許可しない
        $this->request->allowMethod(['post']);

        $this->loadModel('RetentionHistories');

        $data = $this->request->getParsedBody();
        $titleId = $this->request->getData('title_id', '');

        // バリデーションエラーの場合はそのまま返す
        if (($errors = $this->RetentionHistories->validator()->errors($data))) {
            $this->Flash->error($errors);
            return $this->setTabAction('detail', 'histories', $titleId, false);
        }

        // すでに存在するかどうかを確認
		if (!$this->RetentionHistories->add($data)) {
            $this->Flash->error(__("タイトル保持情報がすでに存在します。タイトルID：{$titleId}"));
            return $this->setTabAction('detail', 'histories', $titleId, false);
		}
        $this->Flash->info(__("保持履歴を登録しました。"));

        // POSTされたデータを初期化
        $this->request = $this->request->withParsedBody([]);

        // 詳細情報表示処理へ
        return $this->setTabAction('detail', 'histories', $titleId, false);
	}
}
