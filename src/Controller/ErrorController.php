<?php

namespace App\Controller;

use Exception;
use PDOException;

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
        $this->Log->error(__('Error Code: '.$exception->getCode()));
        $this->Log->error(__($exception->getMessage()));

        if ($exception instanceof PDOException) {
            $this->Flash->error(__("データの保存に失敗しました…。"));
        }
    }
}
