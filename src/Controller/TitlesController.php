<?php

namespace App\Controller;

use Cake\Network\Exception\BadRequestException;

/**
 * タイトル情報コントローラ
 *
 * @author      Kazuki Kamizuru
 * @since       2015/07/25
 *
 * @property \App\Model\Table\TitlesTable $Titles
 */
class TitlesController extends AppController
{
    /**
     * 初期処理
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function index()
    {
        return $this->_setTitle('タイトル情報検索')->render('index');
    }

    /**
     * 詳細表示処理
     *
     * @param int $id 取得するデータのID
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function detail(int $id)
    {
        // セッションから入力値が取得できなければIDで取得
        if (!($title = $this->__readSession())) {
            $title = $this->Titles->get($id);
        }

        return $this->_setDialogMode()
            ->set('title', $title)->render('detail');
    }

    /**
     * 保存処理
     *
     * @return \Psr\Http\Message\ResponseInterface
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
            $this->__writeSession($title)->_setErrors($title->errors());
        } else {
            $this->_setMessages(__("タイトル：{$title->name}を保存しました。"));
        }

        return $this->setAction('detail', $title->id);
    }

    /**
     * 入力値をセッションに設定します。
     *
     * @param \App\Model\Entity\Title $title
     * @return \Cake\Controller\Controller
     */
    private function __writeSession($title)
    {
        $this->request->session()->write('title', $title);
        return $this;
    }

    /**
     * 入力値をセッションから取得します。
     *
     * @return \App\Model\Entity\Title|null
     */
    private function __readSession()
    {
        return $this->request->session()->consume('title');
    }
}
