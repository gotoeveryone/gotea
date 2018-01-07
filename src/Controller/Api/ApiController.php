<?php

namespace Gotea\Controller\Api;

use Cake\Controller\Controller;
use Cake\Event\EventManager;
use Gotea\Controller\SecureTrait;
use Gotea\Event\LoggedUser;

/**
 * API基底コントローラ
 *
 * @property \Cake\Controller\Component\RequestHandlerComponent $RequestHandler
 */
abstract class ApiController extends Controller
{
    use SecureTrait;

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        $this->forceSSL();
        $this->loadComponent('RequestHandler');

        // 当アクションのレスポンスはすべてJSON形式
        $this->response = $this->response->withType('application/json');
        $this->RequestHandler->renderAs($this, 'json');

        // 操作ユーザ記録イベントを設定
        if (($user = $this->request->getHeaderLine('X-Access-User'))) {
            EventManager::instance()->on(new LoggedUser([
                'account' => $user,
            ]));
        }
    }

    /**
     * エラーレスポンスを生成します。
     *
     * @param int $code ステータスコード
     * @param string|null $message メッセージ
     * @return \Cake\Http\Response
     */
    protected function renderError($code = 500, $message = null)
    {
        $this->response = $this->response->withStatus($code);

        if (!$message) {
            $message = $this->response->getReasonPhrase();
        }

        return $this->renderJson([
            'code' => $code,
            'message' => $message,
        ]);
    }

    /**
     * JSONレスポンスを返却します。
     *
     * @param array $json JSONデータ
     * @return \Cake\Http\Response
     */
    protected function renderJson($json = [])
    {
        return $this->set([
            'response' => $json,
            '_serialize' => true,
        ])->render();
    }
}
