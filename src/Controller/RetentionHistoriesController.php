<?php
declare(strict_types=1);

namespace Gotea\Controller;

use Cake\Event\EventInterface;
use Cake\Http\Response;

/**
 * タイトル保持履歴コントローラ
 *
 * @author      Kazuki Kamizuru
 * @since       2017/07/22
 * @property \Gotea\Model\Table\RetentionHistoriesTable $RetentionHistories
 */
class RetentionHistoriesController extends AppController
{
    /**
     * @inheritDoc
     */
    public function beforeFilter(EventInterface $event)
    {
        $this->Authorization->authorize($this->request, 'access');

        parent::beforeFilter($event);
    }

    /**
     * 登録・更新処理
     *
     * @param int $id タイトルID
     * @return \Cake\Http\Response|null
     */
    public function save(int $id): ?Response
    {
        // エンティティ取得 or 生成
        $historyId = $this->getRequest()->getData('id', '');
        $data = $this->getRequest()->getParsedBody();
        $history = $this->RetentionHistories->findOrNew(['id' => $historyId]);
        $this->RetentionHistories->patchEntity($history, $data);
        $history->title_id = $id;
        $newest = $data['newest'] ?? false;
        $history->newest = is_scalar($newest) && filter_var($newest, FILTER_VALIDATE_BOOLEAN);

        // 保存
        if (!$this->RetentionHistories->save($history)) {
            $this->setErrors(400, $history->getErrors());
        } else {
            $this->setMessages(__('The retention history is saved'));
        }

        return $this->redirect([
            '_name' => 'view_title',
            '?' => ['tab' => 'retention_histories'],
            $id,
        ]);
    }
}
