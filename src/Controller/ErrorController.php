<?php
declare(strict_types=1);

namespace Gotea\Controller;

use Cake\Controller\ErrorController as BaseErrorController;
use Cake\Log\Log;
use PDOException;
use Throwable;

/**
 * アプリの共通例外コントローラ
 *
 * @author  Kazuki Kamizuru
 * @since   2016/12/28
 */
class ErrorController extends BaseErrorController
{
    use JsonResponseTrait;

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Flash');
    }

    /**
     * ロールバック処理を行います。
     *
     * @param \Exception $exception 例外
     * @return void
     */
    public function rollback(Throwable $exception)
    {
        Log::error("Error Code: {$exception->getCode()}");
        Log::error($exception->getMessage());

        if ($exception instanceof PDOException) {
            $this->Flash->error(__('Executing query failed.<br/>Please confirm logfile.'));
        }
    }
}
