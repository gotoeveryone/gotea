<?php

namespace Gotea\Controller;

/**
 * JSON形式のレスポンスを制御します。
 *
 * @property \Cake\Controller\Component\RequestHandlerComponent $RequestHandler
 */
trait JsonResponseTrait
{
    /**
     * アクションのレスポンスをすべてJSON形式に設定します。
     *
     * @return void
     */
    public function forceJsonResponse()
    {
        $this->loadComponent('RequestHandler');

        $this->setResponse($this->getResponse()->withType('application/json'));
        $this->RequestHandler->renderAs($this, 'json');
    }

    /**
     * エラーレスポンスを生成します。
     *
     * @param int|null $code ステータスコード
     * @param string|array|null $message メッセージ
     * @return \Cake\Http\Response
     */
    public function renderError(?int $code = 500, $message = null)
    {
        if ($code === 0) {
            $code = 500;
        }

        $this->setResponse($this->getResponse()->withStatus($code));

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
    public function renderJson($json = [])
    {
        return $this->set([
            'response' => $json,
            '_serialize' => true,
        ])->render();
    }
}
