<?php

namespace Gotea\Error;

use Cake\Error\ExceptionRenderer;

/**
 * Exception発生時のレンダラクラス
 *
 * @author  Kazuki Kamizuru
 * @since   2016/12/28
 */
class AppExceptionRenderer extends ExceptionRenderer
{
    /**
     * {@inheritdoc}
     *
     * @see \Gotea\Controller\ErrorController
     */
    public function render()
    {
        $this->controller->rollback($this->error);

        // JSONリクエストの場合
        if ($this->controller->getRequest()->is('json')) {
            return $this->controller->renderError($this->error->getCode());
        }

        return parent::render();
    }
}
