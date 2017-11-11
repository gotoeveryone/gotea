<?php

namespace Gotea\Controller;

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
     * @param int $id タイトルID
     * @return \Cake\Http\Response|null
     */
    public function create(int $id)
    {
        // 保存
        $history = $this->RetentionHistories->newEntity($this->request->getParsedBody());
        $history->title_id = $id;
        if (!$this->RetentionHistories->save($history)) {
            $this->_setErrors($history->errors());
        } else {
            $this->_setMessages(__("保持履歴を登録しました。"));
        }

        return $this->redirect([
            '_name' => 'view_title',
            '?' => ['tab' => 'histories'],
            $id,
        ]);
    }
}
