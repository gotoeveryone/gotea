<?php

namespace App\Error;

use Cake\Error\BaseErrorHandler;
use Psr\Log\LogLevel;

/**
 * エラーハンドラ
 */
class AppError extends BaseErrorHandler
{
    public function _displayError($error, $debug)
    {
        $this->_logError(LogLevel::ERROR, $error);
        return 'エラーが発生しました。';
    }

    public function _displayException($exception)
    {
        $this->_logError(LogLevel::ERROR, $exception);
        return '例外が発生しました。';
    }
}