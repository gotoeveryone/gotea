<?php

namespace Gotea\Controller;

use Exception;
use PDOException;
use Cake\Log\Log;

/**
 * アプリの共通例外コントローラ
 *
 * @author  Kazuki Kamizuru
 * @since   2016/12/28
 */
class ErrorController extends AppController
{
	/**
	 * ロールバック処理を行います。
     *
     * @param Exception $exception
     */
    public function _rollback(Exception $exception)
    {
        Log::error(__('Error Code: '.$exception->getCode()));
        Log::error(__($exception->getMessage()));

        if ($exception instanceof PDOException) {
            $this->Flash->error(__("データの保存に失敗しました…。"));
        }
    }
}
