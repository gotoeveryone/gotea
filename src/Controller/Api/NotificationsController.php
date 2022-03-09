<?php
declare(strict_types=1);

namespace Gotea\Controller\Api;

use Cake\Http\Response;

/**
 * Notifications Controller
 *
 * @property \Gotea\Model\Table\NotificationsTable $Notifications
 * @method \Gotea\Model\Entity\Notification[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class NotificationsController extends ApiController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response
     */
    public function index(): Response
    {
        $query = $this->Notifications->find()->orderDesc('published');
        $notifications = $this->paginate($query);

        return $this->renderJson([
            'total' => $query->count(),
            'items' => $notifications->map(function ($item) {
                return $item->toArray();
            }),
        ]);
    }

    /**
     * Show method
     *
     * @param int $id ID
     * @return \Cake\Http\Response
     */
    public function view(int $id): Response
    {
        $notification = $this->Notifications->get($id, [
            'contain' => [],
        ]);

        return $this->renderJson($notification->toArray());
    }
}
