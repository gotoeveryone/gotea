<?php

namespace App\Controller;

use Cake\Event\Event;

/**
 * 各種情報クエリ更新用コントローラ
 *
 * @author		Kazuki Kamizuru
 * @since		2015/07/26
 */
class ExecuteQueryController extends AppController {
    public $uses = array('TransactionManager');

	/**
	 * 描画前処理
	 */
    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);
        $this->set('cakeDescription', '各種情報クエリ更新');
    }

    public function index() {
        if ($this->request->data) {
            // トリムし、改行・タブ・全角スペースがあれば除去
            $updateText = str_replace(["\r", "\n", "\t", '　'], '', trim($this->request->data('updateText')));
            // $this->log($updateText, LOG_DEBUG);
            // 「;」で分割
            $queryArray = explode(';', $updateText);
            $counter = 0;

            // トランザクションの開始
            $transaction = $this->TransactionManager->begin();

            try {
                foreach ($queryArray as $query) {
                    if (!empty($query)) {
                        // $this->log($query, LOG_DEBUG);
                        // 更新
                        $this->TransactionManager->query($query, false);
                        // $this->log($this->TransactionManager->getAffectedRows(), LOG_DEBUG);
                        if ($this->TransactionManager->getAffectedRows() != 1) {
                            throw new PDOException('レコードの更新エラー');
                        }
                        $counter++;
                    }
                }
                $this->TransactionManager->commit($transaction);
                $this->Flash->setFlash($counter.'件のレコードを更新しました。');
            } catch (PDOException $e) {
    			$this->log('クエリ登録・更新エラー：'.$e->getMessage());
                $this->TransactionManager->rollback($transaction);
                $this->Flash->error('レコードの更新に失敗しました…。');
            }
        }
    }
}
