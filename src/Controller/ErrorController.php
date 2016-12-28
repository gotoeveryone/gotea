<?php

namespace App\Controller;

use Exception;
use PDOException;
use Cake\Event\Event;

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
        $this->Log->error(__('例外が発生したので、トランザクションをロールバックしました。'));
        $this->Log->error(__('Error Code: '.$exception->getCode()));

        if ($exception instanceof PDOException) {
            $this->Flash->error(__("データの保存に失敗しました…。"));
        }

        // トランザクションをロールバック
        $this->Transaction->rollback();
    }
}