<?php
declare(strict_types=1);

namespace Gotea\Controller\Api;

use Cake\Controller\Controller;
use Cake\Event\EventManager;
use Gotea\Controller\JsonResponseTrait;
use Gotea\Event\LoggedUser;

/**
 * API基底コントローラ
 */
abstract class ApiController extends Controller
{
    use JsonResponseTrait;

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->forceJsonResponse();

        // 操作ユーザ記録イベントを設定
        $user = $this->getRequest()->getHeaderLine('X-Access-User');
        if ($user) {
            // モデル側のインスタンスイベントより先に実行する必要があるため、グローバルイベントマネージャに登録する
            EventManager::instance()->on(new LoggedUser([
                'account' => $user,
            ]));
        }
    }
}
