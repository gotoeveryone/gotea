<?php
declare(strict_types=1);

namespace Gotea\Controller;

use Cake\Http\Response;
use Cake\View\JsonView;
use JsonSerializable;

/**
 * JSON形式のレスポンスを制御します。
 */
trait JsonResponseTrait
{
    /**
     * @inheritDoc
     */
    public function viewClasses(): array
    {
        return [JsonView::class];
    }

    /**
     * エラーレスポンスを生成します。
     *
     * @param int|null $code ステータスコード
     * @param array|string|null $message メッセージ
     * @return \Cake\Http\Response
     */
    public function renderError(?int $code = 500, string|array|null $message = null): Response
    {
        if ($code === 0) {
            $code = 500;
        }

        $this->setResponse($this->getResponse()->withStatus($code));

        if (!$message) {
            $message = $this->getResponse()->getReasonPhrase();
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
    public function renderJson(array|JsonSerializable $json = []): Response
    {
        return $this->response->withStringBody(json_encode([
            'response' => $json,
            // '_serialize' => true,
        ]));
    }
}
