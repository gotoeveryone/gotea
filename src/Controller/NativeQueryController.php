<?php

namespace App\Controller;

use PDOException;
use Cake\Http\Response;

/**
 * 各種情報クエリ更新用コントローラ
 *
 * @author  Kazuki Kamizuru
 * @since   2015/07/26
 */
class NativeQueryController extends AppController
{
	/**
	 * 初期処理
     *
     * @return Response
	 */
    public function index()
    {
        // 実行処理
        if ($this->request->isPost()) {
            // トリムし、改行・タブ・全角スペースがあれば除去
            $updateText = str_replace(["\r", "\n", "\t", '　'], '',
                    trim($this->request->getData('queries')));
            // 「;」で分割
            $queries = explode(';', trim($updateText));

            try {
                // クエリの実行
                $count = $this->__executeQueries($queries);
                $this->Flash->info(__("{$count}件のクエリを実行しました。"));
            } catch (PDOException $e) {
                $this->Transaction->markToRollback();
                $this->Flash->error(__("レコードの更新に失敗しました…。<br>ログを確認してください。"));
            }
        }

        $this->_setTitle('各種情報クエリ更新');
        return $this->render();
    }

    /**
     * クエリを実行し、件数を返します。
     *
     * @param array $queries
     * @return int
     * @throws PDOException
     */
    private function __executeQueries($queries)
    {
        $counter = 0;
        foreach ($queries as $query) {
            if (empty($query)) {
                continue;
            }

            // 更新
            $cnt = $this->Transaction->getConnection()->execute($query)->count();
            if ($cnt !== 1) {
                throw new PDOException(__("レコードの更新エラー"));
            }
            $counter++;
        }
        return $counter;
    }
}
