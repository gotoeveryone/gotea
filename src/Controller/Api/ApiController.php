<?php

namespace Gotea\Controller\Api;

use Cake\Controller\Controller;
use Cake\Event\EventManager;
use Gotea\Controller\JsonResponseTrait;
use Gotea\Controller\SecureTrait;
use Gotea\Event\LoggedUser;

/**
 * API基底コントローラ
 */
abstract class ApiController extends Controller
{
    use JsonResponseTrait;
    use SecureTrait;

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        $this->forceSSL();
        $this->forceJsonResponse();

        // 操作ユーザ記録イベントを設定
        if (($user = $this->getRequest()->getHeaderLine('X-Access-User'))) {
            // モデル側のインスタンスイベントより先に実行する必要があるため、グローバルイベントマネージャに登録する
            EventManager::instance()->on(new LoggedUser([
                'account' => $user,
            ]));
        }
    }
}
