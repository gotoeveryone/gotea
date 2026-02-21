<?php
declare(strict_types=1);

namespace Gotea\Controller\Api;

use Cake\Http\Response;

/**
 * Notifications Controller
 *
 * @property \Gotea\Model\Table\NotificationsTable $Notifications
 * @method \Cake\Datasource\Paging\PaginatedInterface paginate($object = null, array $settings = [])
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
        $query = $this->Notifications->find()->orderByDesc('published');
        $notifications = $this->paginate($query);

        return $this->renderJson([
            'total' => $query->count(),
            'items' => collection($notifications->items())->map(function ($item) {
                return $item->toArray();
            })->toList(),
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
        $notification = $this->Notifications->get($id);

        return $this->renderJson($notification->toArray());
    }
}
