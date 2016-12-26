<?php

namespace App\Controller;

use PDOException;
use Cake\Datasource\ConnectionInterface;
use Cake\Event\Event;

/**
 * 各種情報クエリ更新用コントローラ
 *
 * @author		Kazuki Kamizuru
 * @since		2015/07/26
 */
class NativeQueryController extends AppController
{
	/**
	 * 描画前処理
	 */
    public function beforeRender(Event $event)
    {
        $this->_setTitle('各種情報クエリ更新');
        parent::beforeRender($event);
    }

	/**
	 * 初期処理
	 */
    public function index()
    {
        return $this->render('index');
    }

	/**
	 * クエリ実行処理
	 */
    public function execute()
    {
        // トリムし、改行・タブ・全角スペースがあれば除去
        $updateText = str_replace(["\r", "\n", "\t", '　'], '', trim($this->request->data('executeTargets')));
        // 「;」で分割
        $queries = explode(';', $updateText);
        $conn = $this->_getConnection();
        try {
            // クエリの実行
            $count = $this->__executeQueries($conn, $queries);
            $this->Flash->info(__("{$count}件のクエリを実行しました。"));
        } catch (PDOException $e) {
            $this->Log->info(__("クエリ実行エラー：{$e->getMessage()}"));
            $this->_markToRollback();
            $this->Flash->error(__("レコードの更新に失敗しました…。"));
        } finally {
            return $this->index();
        }
    }

    /**
     * クエリを実行し、件数を返します。
     * 
     * @param ConnectionInterface $conn
     * @param array $queries
     * @return int
     * @throws PDOException
     */
    private function __executeQueries(ConnectionInterface $conn, $queries)
    {
        $counter = 0;
        foreach ($queries as $query) {
            if (empty($query)) {
                continue;
            }

            // 更新
            if (count($stmt = $conn->execute($query)) !== 1) {
                throw new PDOException(__("レコードの更新エラー"));
            }
            $counter++;
        }
        return $counter;
    }
}
