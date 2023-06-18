<?php
declare(strict_types=1);

namespace Gotea\Controller;

use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\I18n\FrozenTime;
use Cake\Log\Log;
use Exception;
use Gotea\Client\TwitterClient;
use Gotea\Model\Entity\Notification;

/**
 * Notifications Controller
 *
 * @property \Gotea\Model\Table\NotificationsTable $Notifications
 * @method \Gotea\Model\Entity\Notification[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class NotificationsController extends AppController
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
     * 一覧表示処理
     *
     * @return \Cake\Http\Response|null
     */
    public function index(): ?Response
    {
        return $this->renderWith('お知らせ一覧', 'index');
    }

    /**
     * 新規登録画面表示処理
     *
     * @return \Cake\Http\Response|null
     */
    public function new(): ?Response
    {
        $data = [];

        // コピー元の指定があればデータを取得して反映
        $fromId = $this->getRequest()->getQuery('from');
        if ($fromId) {
            $notification = $this->Notifications->findById($fromId)->first();
            if ($notification) {
                $data = $notification->toArray();
                # 投稿日時は現在日時を設定しておく
                $data['published'] = FrozenTime::now();
            }
        }

        $notification = $this->Notifications->newEntity($data);

        $this->set(compact('notification'));

        return $this->renderWith('お知らせ追加');
    }

    /**
     * 編集画面表示処理
     *
     * @param int $id サロゲートキー
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function edit(int $id): ?Response
    {
        $notification = $this->Notifications->get($id, [
            'contain' => [],
        ]);

        $this->set('notification', $notification);

        return $this->renderWith('お知らせ編集');
    }

    /**
     * 新規登録処理
     *
     * @return \Cake\Http\Response|null
     */
    public function create(): ?Response
    {
        $notification = $this->Notifications->newEntity($this->getRequest()->getData());
        if (!$this->Notifications->save($notification)) {
            $this->set(compact('notification'));

            return $this->renderWithErrors(400, $notification->getErrors(), 'お知らせ追加', 'new');
        }

        if ($notification->is_published) {
            $this->postTwitter($notification);
        }

        $this->Flash->success(__('The notification has been saved.'));

        return $this->redirect(['_name' => 'notifications']);
    }

    /**
     * 更新処理
     *
     * @param int $id サロゲートキー
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function update(int $id): ?Response
    {
        $notification = $this->Notifications->get($id);
        $isPublished = $notification->is_published;
        $notification = $this->Notifications->patchEntity($notification, $this->getRequest()->getData());
        if (!$this->Notifications->save($notification)) {
            $this->set(compact('notification'));

            return $this->renderWithErrors(400, $notification->getErrors(), 'お知らせ編集', 'edit');
        }

        if (!$isPublished && $notification->is_published) {
            $this->postTwitter($notification);
        }

        $this->Flash->success(__('The notification has been saved.'));

        return $this->redirect(['_name' => 'notifications']);
    }

    /**
     * 削除処理
     *
     * @param int $id サロゲートキー
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function delete(int $id): ?Response
    {
        $notification = $this->Notifications->get($id);
        if ($this->Notifications->delete($notification)) {
            $this->Flash->success(__('The notification has been deleted.'));
        } else {
            $this->Flash->error(__('The notification could not be deleted. Please, try again.'));
        }

        return $this->redirect(['_name' => 'notifications']);
    }

    /**
     * Twitter へ投稿する
     *
     * @param \Gotea\Model\Entity\Notification $data お知らせ
     * @return void
     */
    private function postTwitter(Notification $data): void
    {
        $informationsUrl = rtrim(Configure::read('App.gotoeveryone.informationsUrl', '/'));
        $message = "{$data->title}\n{$informationsUrl}/{$data->id}";

        try {
            $client = new TwitterClient();
            $response = $client->post($message);
            // 処理自体は続行させたいので、エラーがあった場合でもログ出力のみ行う
            if (!empty($response->errors[0]->message)) {
                Log::warning($response->errors[0]->message);
            }
        } catch (Exception $e) {
            Log::warning($e->getMessage());
        }
    }
}
