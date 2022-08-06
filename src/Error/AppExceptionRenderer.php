<?php
declare(strict_types=1);

namespace Gotea\Error;

use Cake\Error\Renderer\WebExceptionRenderer;
use PDOException;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LogLevel;

/**
 * Exception発生時のレンダラクラス
 *
 * @property \Gotea\Controller\ErrorController $controller
 */
class AppExceptionRenderer extends WebExceptionRenderer
{
    /**
     * @inheritDoc
     */
    public function render(): ResponseInterface
    {
        if ($this->controller->getRequest()->is('json')) {
            return $this->controller->renderError($this->error->getCode());
        }

        if ($this->error instanceof PDOException) {
            $this->controller->log($this->error->getMessage(), LogLevel::ERROR);
            $this->controller->Flash->error(__('Executing query failed.<br/>Please confirm logfile.'));
        }

        return parent::render();
    }
}
