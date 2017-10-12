<?php

namespace Gotea\Controller;

use Cake\Network\Exception\BadRequestException;

/**
 * タイトル情報コントローラ
 *
 * @author      Kazuki Kamizuru
 * @since       2015/07/25
 *
 * @property \Gotea\Model\Table\TitlesTable $Titles
 */
class TitlesController extends AppController
{
    /**
     * 初期処理
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        return $this->_renderWith('タイトル情報検索');
    }

    /**
     * 詳細表示処理
     *
     * @param int $id 取得するデータのID
     * @return \Cake\Http\Response|null
     */
    public function detail(int $id)
    {
        // セッションから入力値が取得できなければIDで取得
        if (!($title = $this->_consumeBySession('title'))) {
            $title = $this->Titles->get($id);
        }

        return $this->set('title', $title)->_renderWithDialog('detail');
    }

    /**
     * 保存処理
     *
     * @return \Cake\Http\Response|null
     */
    public function save()
    {
        // POST以外は許可しない
        $this->request->allowMethod(['post']);

        // 現状は更新のみなので、IDが取得できなければエラー
        if (!($id = $this->request->getData('id'))) {
            throw new BadRequestException(__('IDは必須です。'));
        }

        // データ取得
        $title = $this->Titles->get($id);
        $this->Titles->patchEntity($title, $this->request->getParsedBody());

        // 保存
        if (!$this->Titles->save($title)) {
            $this->_writeToSession('title', $title)->_setErrors($title->errors());
        } else {
            $this->_setMessages(__('[{0}: {1}] を保存しました。', $title->id, $title->name));
        }

        return $this->setAction('detail', $title->id);
    }
}
