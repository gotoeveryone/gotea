<?php

namespace Gotea\Controller;

use Cake\Network\Exception\BadRequestException;

/**
 * タイトル保持履歴コントローラ
 *
 * @author      Kazuki Kamizuru
 * @since       2017/07/22
 *
 * @property \Gotea\Model\Table\RetentionHistoriesTable $RetentionHistories
 */
class RetentionHistoriesController extends AppController
{
    /**
     * 登録処理
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function add()
    {
        // POST以外は許可しない
        $this->request->allowMethod(['post']);

        // タイトルIDが取得できなければエラー
        if (!($id = $this->request->getData('title_id'))) {
            throw new BadRequestException(__('タイトルIDは必須です。'));
        }

        // 保存
        $history = $this->RetentionHistories->newEntity($this->request->getParsedBody());
        if (!$this->RetentionHistories->save($history)) {
            $this->_setErrors($history->errors());
        } else {
            $this->_setMessages(__("保持履歴を登録しました。"));
        }

        return $this->redirect([
            'controller' => 'Titles',
            'action' => 'detail',
            '?' => ['tab' => 'histories'],
            $id,
        ]);
    }
}
