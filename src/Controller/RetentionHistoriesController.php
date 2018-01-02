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
     * 登録・更新処理
     *
     * @param int $id タイトルID
     * @return \Cake\Http\Response|null
     */
    public function save(int $id)
    {
        // エンティティ取得 or 生成
        $historyId = $this->request->getData('id');
        $history = $this->RetentionHistories->findOrNew(['id' => $historyId]);
        $this->RetentionHistories->patchEntity($history, $this->request->getParsedBody());
        $history->title_id = $id;

        // 保存
        if (!$this->RetentionHistories->save($history)) {
            $this->_setErrors(400, $history->getErrors());
        } else {
            $this->_setMessages(__('The retention history is saved'));
        }

        return $this->redirect([
            '_name' => 'view_title',
            '?' => ['tab' => 'histories'],
            $id,
        ]);
    }
}
