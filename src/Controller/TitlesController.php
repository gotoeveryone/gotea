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
 * @property \App\Model\Table\CountriesTable $Countries
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
        $this->_setTitle('タイトル情報検索');
        return $this->render();
    }

	/**
	 * 詳細情報表示・更新処理
     *
     * @param int|null $id 取得するデータのID
     * @return Response
	 */
	public function detail($id = null)
    {
        // ダイアログ表示
        $this->_setDialogMode();

        // 保存処理
        if ($id === null && $this->request->isPost()) {
            // バリデーションエラーの場合は処理終了
            $data = $this->request->getParsedBody();
            if (($errors = $this->Titles->validator()->errors($data))) {
                $this->Flash->error($errors);
                return $this->render();
            }

            // 保存処理
            if (($title = $this->Titles->saveEntity($data))) {
                $this->Flash->info(__("タイトル：{$title->name}を更新しました。"));
            }
        } elseif (!($title = $this->Titles->findWithRelations($id))) {
            throw new NotFoundException(__("タイトル情報が取得できませんでした。ID：{$id}"));
        }

        $this->set('title', $title);

        return $this->render();
    }

	/**
	 * タイトル保持情報の登録
     *
     * @return Response
	 */
	public function addHistory()
    {
        $this->loadModel('RetentionHistories');
        $this->loadModel('Countries');

        $data = $this->request->getParsedBody();
        $titleId = $data['title_id'] ?? '';

        // バリデーションエラーの場合はそのまま返す
        if (($errors = $this->RetentionHistories->validator()->errors($data))) {
            $this->Flash->error($errors);
            return $this->setTabAction('detail', 'histories', $titleId);
        }

        // すでに存在するかどうかを確認
		if (!$this->RetentionHistories->add($data)) {
            $this->Flash->error(__("タイトル保持情報がすでに存在します。タイトルID：{$titleId}"));
            return $this->setTabAction('detail', 'histories', $titleId);
		}
        $this->Flash->info(__("保持履歴を登録しました。"));

        // POSTされたデータを初期化
        $this->request = $this->request->withParsedBody([]);

        // 詳細情報表示処理へ
        return $this->setTabAction('detail', 'histories', $titleId);
	}
}
