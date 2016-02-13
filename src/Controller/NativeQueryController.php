<?php

namespace App\Controller;

use Cake\Datasource\ConnectionManager;
use PDOException;
use Cake\Event\Event;

/**
 * 各種情報クエリ更新用コントローラ
 *
 * @author		Kazuki Kamizuru
 * @since		2015/07/26
 */
class ExecuteQueryController extends AppController
{
	/**
	 * 描画前処理
	 */
    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);
        $this->set('cakeDescription', '各種情報クエリ更新');
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
        // $this->log($updateText, LOG_DEBUG);
        // 「;」で分割
        $queryArray = explode(';', $updateText);
        $counter = 0;

        try {
            foreach ($queryArray as $query) {
                if (!empty($query)) {
                    // 更新
                    $stmt = ConnectionManager::get('default')->execute($query);
//                    $this->log('クエリ：'.$query.' 件数：'.count($stmt), LogLevel::INFO);
                    if (count($stmt) !== 1) {
                        throw new PDOException('レコードの更新エラー');
                    }
                    $counter++;
                }
            }
            $this->Flash->info($counter.'件のレコードを更新しました。');
        } catch (PDOException $e) {
            $this->log('クエリ実行エラー：'.$e->getMessage());
            $this->isRollback = true;
            $this->Flash->error('レコードの更新に失敗しました…。');
        } finally {
            return $this->index();
        }
    }
}
