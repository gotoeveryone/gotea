<?php

namespace Gotea\Controller;

use Cake\Controller\ErrorController as BaseErrorController;
use Cake\Log\Log;
use Exception;
use PDOException;

/**
 * アプリの共通例外コントローラ
 *
 * @author  Kazuki Kamizuru
 * @since   2016/12/28
 */
class ErrorController extends BaseErrorController
{
    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('Flash');
    }

    /**
     * ロールバック処理を行います。
     *
     * @param Exception $exception 例外
     * @return void
     */
    public function rollback(Exception $exception)
    {
        Log::error("Error Code: {$exception->getCode()}");
        Log::error($exception->getMessage());

        if ($exception instanceof PDOException) {
            $this->Flash->error(__('Executing query failed.<br/>Please confirm logfile.'));
        }
    }
}
