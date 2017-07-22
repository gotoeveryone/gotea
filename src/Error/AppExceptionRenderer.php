<?php

namespace App\Error;

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
     * @see \App\Controller\ErrorController
     */
    public function render()
    {
        $this->_getController()->_rollback($this->error);
        return parent::render();
    }
}
