<?php
namespace Gotea\Controller;

use Gotea\Controller\AppController;

/**
 * Notifications Controller
 *
 * @property \Gotea\Model\Table\NotificationsTable $Notifications
 *
 * @method \Gotea\Model\Entity\Notification[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class NotificationsController extends AppController
{
    /**
     * 一覧表示処理
     *
     * @return \Cake\Http\Response
     */
    public function index()
    {
        $notifications = $this->paginate($this->Notifications->findAllNewestArrivals());

        $this->set(compact('notifications'));

        return $this->renderWith('お知らせ一覧', 'index');
    }

    /**
     * 新規登録画面表示処理
     *
     * @return \Cake\Http\Response
     */
    public function new()
    {
        $notification = $this->Notifications->newEntity();

        $this->set(compact('notification'));

        return $this->renderWith('お知らせ追加');
    }

    /**
     * 編集画面表示処理
     *
     * @param int $id サロゲートキー
     * @return \Cake\Http\Response
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function edit($id = null)
    {
        $notification = $this->Notifications->get($id, [
            'contain' => []
        ]);

        $this->set('notification', $notification);

        return $this->renderWith('お知らせ編集');
    }

    /**
     * 新規登録処理
     *
     * @return \Cake\Http\Response
     */
    public function create()
    {
        $notification = $this->Notifications->newEntity($this->request->getData());
        if (!$this->Notifications->save($notification)) {
            $this->set(compact('notification'));

            return $this->renderWithErrors(400, $notification->getErrors(), 'お知らせ追加', 'new');
        }

        $this->Flash->success(__('The notification has been saved.'));

        return $this->redirect(['_name' => 'notifications']);
    }

    /**
     * 更新処理
     *
     * @param int $id サロゲートキー
     * @return \Cake\Http\Response
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function update($id = null)
    {
        $notification = $this->Notifications->get($id);
        $notification = $this->Notifications->patchEntity($notification, $this->request->getData());
        if (!$this->Notifications->save($notification)) {
            $this->set(compact('notification'));

            return $this->renderWithErrors(400, $notification->getErrors(), 'お知らせ編集', 'edit');
        }

        $this->Flash->success(__('The notification has been saved.'));

        return $this->redirect(['_name' => 'notifications']);
    }

    /**
     * 削除処理
     *
     * @param int $id サロゲートキー
     * @return \Cake\Http\Response
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function delete($id = null)
    {
        $notification = $this->Notifications->get($id);
        if ($this->Notifications->delete($notification)) {
            $this->Flash->success(__('The notification has been deleted.'));
        } else {
            $this->Flash->error(__('The notification could not be deleted. Please, try again.'));
        }

        return $this->redirect(['_name' => 'notifications']);
    }
}
