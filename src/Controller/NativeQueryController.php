<?php

namespace Gotea\Controller;

use Cake\Datasource\ConnectionInterface;
use Cake\Datasource\ConnectionManager;
use Cake\Log\Log;
use PDOException;

/**
 * 各種情報クエリ更新用コントローラ
 *
 * @author  Kazuki Kamizuru
 * @since   2015/07/26
 */
class NativeQueryController extends AppController
{
    /**
     * 初期表示処理
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function index()
    {
        return $this->renderWith('各種情報クエリ更新');
    }

    /**
     * クエリ実行処理
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function execute()
    {
        // トリムし、改行・タブ・全角スペースがあれば除去
        $updateText = str_replace(["\r", "\n", "\t", '　'], '', trim($this->request->getData('queries')));
        // 「;」で分割
        $queries = explode(';', trim($updateText));

        $conn = ConnectionManager::get('default');
        try {
            $count = $conn->transactional(function (ConnectionInterface $conn) use ($queries) {
                return $this->executeQueries($conn, $queries);
            });
            $this->Flash->info(__('Executed {0} queries', $count));
        } catch (PDOException $e) {
            Log::error($e);
            $this->Flash->error(__('Executing query failed.<br/>Please confirm logfile.'));
        }

        return $this->renderWith('各種情報クエリ更新', 'index');
    }

    /**
     * クエリを実行し、件数を返します。
     *
     * @param \Cake\Datasource\ConnectionInterface $conn コネクション
     * @param array $queries 実行クエリ
     * @return int 更新件数
     * @throws \PDOException
     */
    private function executeQueries(ConnectionInterface $conn, $queries)
    {
        $counter = 0;
        foreach ($queries as $query) {
            if (empty($query)) {
                continue;
            }

            // 更新
            $cnt = $conn->execute($query)->count();
            if ($cnt !== 1) {
                throw new PDOException(__('Updated record count more than {0}', 1));
            }
            $counter++;
        }

        return $counter;
    }
}
