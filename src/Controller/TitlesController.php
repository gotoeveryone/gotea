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
    public function view(int $id)
    {
        // セッションから入力値が取得できなければIDで取得
        if (!($title = $this->_consumeBySession('title'))) {
            $title = $this->Titles->get($id);
        }

        return $this->set('title', $title)->_renderWithDialog();
    }

    /**
     * 更新処理
     *
     * @param int $id タイトルID
     * @return \Cake\Http\Response|null
     */
    public function update(int $id)
    {
        // データ取得
        $title = $this->Titles->get($id);
        $this->Titles->patchEntity($title, $this->request->getParsedBody());

        // 保存
        if (!$this->Titles->save($title)) {
            return $this->_writeToSession('title', $title)
                ->_setErrors(400, $title->getErrors())
                ->setAction('view', $title->id);
        } else {
            $this->_setMessages(__('The title {0} - {1} is saved', $title->id, $title->name));
        }

        return $this->redirect([
            '_name' => 'view_title',
            $title->id,
        ]);
    }
}
