<?php
declare(strict_types=1);

namespace Gotea\Error;

use Cake\Error\ExceptionRenderer;
use Psr\Http\Message\ResponseInterface;

/**
 * Exception発生時のレンダラクラス
 *
 * @property \Gotea\Controller\ErrorController $controller
 */
class AppExceptionRenderer extends ExceptionRenderer
{
    /**
     * @inheritDoc
     */
    public function render(): ResponseInterface
    {
        $this->controller->rollback($this->error);

        // JSONリクエストの場合
        if ($this->controller->getRequest()->is('json')) {
            return $this->controller->renderError($this->error->getCode());
        }

        return parent::render();
    }
}
