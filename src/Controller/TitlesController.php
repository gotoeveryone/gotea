<?php
declare(strict_types=1);

namespace Gotea\Controller;

use Cake\Event\EventInterface;
use Cake\Http\Response;

/**
 * タイトル情報コントローラ
 *
 * @author      Kazuki Kamizuru
 * @since       2015/07/25
 * @property \Gotea\Model\Table\TitlesTable $Titles
 */
class TitlesController extends AppController
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
     * 初期処理
     *
     * @return \Cake\Http\Response|null
     */
    public function index(): ?Response
    {
        return $this->renderWith('タイトル情報検索');
    }

    /**
     * 詳細表示処理
     *
     * @param int $id 取得するデータのID
     * @return \Cake\Http\Response|null
     */
    public function view(int $id): ?Response
    {
        $title = $this->Titles->findByIdWithRelation($id);

        return $this->set(compact('title'))->renderWithDialog();
    }

    /**
     * 更新処理
     *
     * @param int $id タイトルID
     * @return \Cake\Http\Response|null
     */
    public function update(int $id): ?Response
    {
        // データ取得
        $title = $this->Titles->findByIdWithRelation($id);
        $this->Titles->patchEntity($title, $this->getRequest()->getParsedBody());

        // 保存
        if (!$this->Titles->save($title)) {
            $this->set('title', $title);

            return $this->renderWithDialogErrors(400, $title->getErrors(), 'view');
        } else {
            $this->setMessages(__('The title {0} - {1} is saved', $title->id, $title->name));
        }

        return $this->redirect([
            '_name' => 'view_title',
            $title->id,
        ]);
    }
}
