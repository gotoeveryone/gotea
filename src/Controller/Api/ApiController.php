<?php

namespace Gotea\Controller\Api;

use Cake\Controller\Controller;
use Cake\Event\EventManager;
use Gotea\Event\LoggedUser;

/**
 * API基底コントローラ
 */
abstract class ApiController extends Controller
{
    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');

        // 当アクションのレスポンスはすべてJSON形式
        $this->response->type('application/json');
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
     * @param string $message メッセージ
     * @return \Cake\Http\Response
     */
    protected function _renderError($code = 500, $message = 'Internal Error')
    {
        $this->response = $this->response->withStatus($code);

        return $this->_renderJson([
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
    protected function _renderJson($json = [])
    {
        return $this->set([
            'response' => $json,
            '_serialize' => true,
        ])->render();
    }
}
