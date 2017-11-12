<?php

namespace Gotea\Controller\Api;

use Cake\Controller\Controller;

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

        // ヘッダのユーザIDをセッションに乗せる
        $this->request->session()->write('Api-UserId', $this->request->getHeaderLine('X-Access-User'));

        // 当アクションのレスポンスはすべてJSON形式
        $this->response->type('application/json');
        $this->RequestHandler->renderAs($this, 'json');
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
