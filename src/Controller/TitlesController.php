<?php

namespace App\Controller;

use Cake\Http\Response;
use Cake\Network\Exception\BadRequestException;
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
        return $this->_setTitle('タイトル情報検索')->render('index');
    }

	/**
	 * 詳細表示処理
     *
     * @param int $id 取得するデータのID
     * @param Title|null $title 表示に利用するデータ
     * @return Response
	 */
	public function detail(int $id, $title = null)
    {
        // ダイアログ表示
        $this->_setDialogMode();

        // 表示に利用するデータがあればそれを設定して終了
        if ($title) {
            return $this->set('title', $title)->render('detail');
        }

        return $this->set('title', $this->Titles->get($id))->render('detail');
    }

    /**
     * 保存処理
     *
     * @return Response
     */
    public function save()
    {
        // POST以外は許可しない
        $this->request->allowMethod(['post']);

        // IDが取得できなければエラー
        if (!($id = $this->request->getData('id'))) {
            throw new BadRequestException(__('IDは必須です。'));
        }

        // IDからデータを取得
        $title = $this->Titles->get($id);

        // バリデーション
        $this->Titles->patchEntity($title, $this->request->getParsedBody());
        if (($errors = $title->errors())) {
            return $this->_setErrors($errors)
                ->setAction('detail', $id, $title);
        }

        // 保存して詳細画面へ
        $this->Titles->save($title);
        return $this->_setMessages(__("タイトル：{$title->name}を更新しました。"))
            ->setAction('detail', $id, $title);
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

        // タイトルIDが取得できなければエラー
        if (!($id = $this->request->getData('title_id'))) {
            throw new BadRequestException(__('タイトルIDは必須です。'));
        }

        // バリデーション
        $this->loadModel('RetentionHistories');
        $history = $this->RetentionHistories->newEntity($this->request->getParsedBody());
        if (($errors = $history->errors())) {
            return $this->_setErrors($errors)
                ->setTabAction('detail', 'histories', $id);
        }

        // すでに存在するかどうかを確認
		if (!$this->RetentionHistories->add($history->toArray())) {
            return $this->_setErrors(__('タイトル保持情報がすでに存在します。'))
                ->setTabAction('detail', 'histories', $id);
		}

        // リクエストを初期化して詳細画面に遷移
        return $this->_setMessages(__("保持履歴を登録しました。"))
            ->_resetRequest()
            ->setTabAction('detail', 'histories', $id);
	}
}
