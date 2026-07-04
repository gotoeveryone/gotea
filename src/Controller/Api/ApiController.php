<?php
declare(strict_types=1);

namespace Gotea\Controller\Api;

use Cake\Controller\Controller;
use Cake\Event\EventInterface;
use Cake\Event\EventManager;
use Gotea\Controller\JsonResponseTrait;
use Gotea\Event\LoggedUser;

/**
 * API基底コントローラ
 *
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 * @property \Authorization\Controller\Component\AuthorizationComponent $Authorization
 */
abstract class ApiController extends Controller
{
    use JsonResponseTrait;

    /**
     * 認証・認可なしで利用できる action
     *
     * @var array<string>
     */
    protected array $publicActions = [];

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Authentication.Authentication', [
            'logoutRedirect' => [
                '_name' => 'top',
            ],
        ]);
        $this->loadComponent('Authorization.Authorization');

        // 操作ユーザ記録イベントを設定
        $user = $this->Authentication->getIdentity();
        if ($user) {
            // モデル側のインスタンスイベントより先に実行する必要があるため、グローバルイベントマネージャに登録する
            EventManager::instance()->on(new LoggedUser($user->getOriginalData()));
        }
    }

    /**
     * @inheritDoc
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $action = (string)$this->getRequest()->getParam('action');
        if (in_array($action, $this->publicActions, true)) {
            $this->Authentication->allowUnauthenticated($this->publicActions);
            $this->Authorization->skipAuthorization();

            return;
        }

        $this->Authorization->authorize($this->request, 'access');
    }
}
