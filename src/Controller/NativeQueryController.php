<?php

namespace App\Controller;

use PDOException;
use Cake\Datasource\ConnectionInterface;
use Cake\Datasource\ConnectionManager;
use Cake\Log\Log;

/**
 * 各種情報クエリ更新用コントローラ
 *
 * @author  Kazuki Kamizuru
 * @since   2015/07/26
 */
class NativeQueryController extends AppController
{
	/**
	 * 初期表示・更新処理
     *
     * @return \Psr\Http\Message\ResponseInterface
	 */
    public function index()
    {
        if ($this->request->isPost()) {
            // トリムし、改行・タブ・全角スペースがあれば除去
            $updateText = str_replace(["\r", "\n", "\t", '　'], '',
                    trim($this->request->getData('queries')));
            // 「;」で分割
            $queries = explode(';', trim($updateText));

            $conn = ConnectionManager::get('default');
            try {
                $count = $conn->transactional(function(ConnectionInterface $conn) use ($queries) {
                    // クエリの実行
                    return $this->__executeQueries($conn, $queries);
                });
                $this->Flash->info(__("{$count}件のクエリを実行しました。"));
            } catch (PDOException $e) {
                Log::error($e);
                $this->Flash->error(__("レコードの更新に失敗しました…。<br>ログを確認してください。"));
            }
        }

        return $this->_setTitle('各種情報クエリ更新')->render('index');
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
            $cnt = $conn->execute($query)->count();
            if ($cnt !== 1) {
                throw new PDOException(__("レコードの更新エラー"));
            }
            $counter++;
        }
        return $counter;
    }
}
