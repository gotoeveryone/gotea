<?php
declare(strict_types=1);

namespace Gotea\Controller;

use Abraham\TwitterOAuth\TwitterOAuth;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\I18n\FrozenTime;
use Cake\Log\Log;
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
        $notifications = $this->paginate($this->Notifications->findAllNewestArrivals());

        $this->set(compact('notifications'));

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
     * @param string $id サロゲートキー
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function edit(string $id): ?Response
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
     * @param string $id サロゲートキー
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function update(string $id): ?Response
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
     * @param string $id サロゲートキー
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function delete(string $id): ?Response
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
        if (Configure::read('debug')) {
            return;
        }

        $consumerKey = Configure::read('App.twitter.consumerKey');
        $consumerSecret = Configure::read('App.twitter.consumerSecret');
        $accessToken = Configure::read('App.twitter.accessToken');
        $accessTokenSecret = Configure::read('App.twitter.accessTokenSecret');

        $informationsUrl = rtrim(Configure::read('App.gotoeveryone.informationsUrl'), '/');

        try {
            $client = new TwitterOAuth($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);
            $client->post("statuses/update", [
                'status' => "{$data->title}\n{$informationsUrl}/{$data->id}",
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
