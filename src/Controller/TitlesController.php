<?php

namespace App\Controller;

use Cake\Event\Event;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use Psr\Log\LogLevel;

/**
 * タイトルマスタ用コントローラ
 *
 * @author		Kazuki Kamizuru
 * @since		2015/07/25
 */
class TitlesController extends AppController
{
    // タイトル保持情報テーブル
    private $RetentionHistories = null;

    // 所属国マスタテーブル
    private $Countries = null;

    /**
     * 初期処理
     */
	public function initialize()
    {
        parent::initialize();
        $this->RetentionHistories = TableRegistry::get('RetentionHistories');
        $this->Countries = TableRegistry::get('Countries');
    }

    /**
	 * 描画前処理
	 */
    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);

		// 所属国プルダウン
		$this->set('countries', $this->Countries->findCountryHasFileToArray());
   	}

	/**
	 * 初期処理
	 */
	public function index()
    {
        $this->__initSearch();
		$this->set('searchFlag', false);
        return $this->render('index');
    }

	/**
	 * 検索処理
	 */
	public function search()
    {
        $this->__initSearch();

        // リクエストから値を取得
        $searchCountry = $this->request->data('searchCountry');
        $searchDelete = $this->request->data('searchDelete');

        // タイトル一覧を取得
        if (!count($titles = $this->Titles->findTitlesByCountry($searchCountry, $searchDelete))) {
            $this->Flash->info(__('検索結果が0件でした。'));
        }

        $this->set('titles', $titles);

        // 検索フラグを設定
		$this->set('searchFlag', true);

        // indexページへ描画
        return $this->render('index');
	}

    /**
     * 登録・更新処理
     */
	public function save()
    {
        // 更新対象を取得
        $targets = $this->__getUpdateTargets($this->request->data('titles'), $this->request->data('searchCountry'));

        // 更新件数
        $count = 0;

        try {
			// 件数分処理
			foreach ($targets as $target) {
                // タイトル情報を更新
                $this->Titles->save($target);
                $count++;
			}
            $this->Flash->info(__("{$count}件のタイトルマスタを更新しました。"));
		} catch (PDOException $e) {
            $this->log(__("タイトルマスタ登録・更新エラー：{$e->getMessage()}"), LogLevel::ERROR);
            $this->_markToRollback();
			$this->Flash->error(__("タイトルマスタの更新に失敗しました…。"));
		} finally {
			// indexの処理を行う
			return $this->search();
		}
	}

	/**
	 * 詳細情報表示処理
	 */
	public function detail($id = null)
    {
        $this->set('dialogFlag', true);

		// タイトル情報一式を設定
        if (!($title = $this->Titles->findTitleWithRelations($id))) {
            throw new NotFoundException(__("タイトル情報が取得できませんでした。ID：{$id}"));
        }
        $this->set('title', $title);

        return $this->render('detail');
    }

	/**
	 * タイトル保持情報の登録
	 */
	public function regist()
    {
		// 必須カラムのフィールド
		$titleId = $this->request->data('selectTitleId');
		$holding = $this->request->data('registHolding');

        // すでに存在するかどうかを確認
		if ($this->RetentionHistories->findByKey($titleId, $holding)) {
            $this->Flash->error(__("タイトル保持情報がすでに存在します。タイトルID：{$titleId}"));
			return $this->detail($titleId);
		}

        // エンティティを新規作成し、値を設定
        $titleRetain = $this->RetentionHistories->newEntity();
        $titleRetain->setFromRequest($this->request, $titleId, $holding);

        // バリデーションエラーの場合はそのまま返す
        if (($res = $this->RetentionHistories->validator()->errors($titleRetain->toArray()))) {
            // エラーメッセージを書き込み、詳細情報表示処理へ
            $this->Flash->error(__($this->_getErrorMessage($res)));
            return $this->detail($titleId);
        }

		try {
			// タイトル保持情報の保存
			$this->RetentionHistories->save($titleRetain);
            // 最新を登録する場合はタイトルマスタも書き換え
            if ($this->request->data('registWithMapping') === 'true') {
                $title = $this->Titles->get($titleId);
                $title->holding = $holding;
                $this->Titles->save($title);
            }
			$this->Flash->info(__("タイトル保持情報を登録しました。"));
		} catch (PDOException $e) {
            $this->log(__("タイトル保持情報登録エラー：{$e->getMessage()}"), LogLevel::ERROR);
            $this->_markToRollback();
			$this->Flash->error(__("タイトル保持情報の登録に失敗しました…。"));
		} finally {
			// indexの処理を行う
			$this->detail($titleId);
		}
	}

    /**
     * 検索画面初期処理
     */
    private function __initSearch()
    {
        $this->_setTitle('タイトル情報検索');
    }

    /**
     * 更新対象のタイトル一覧を取得します。
     * 
     * @param array $rows
     * @param type $countryId
     * @return array
     */
    private function __getUpdateTargets(array $rows, $countryId)
    {
        // 登録 or 更新対象の一覧を生成
        $targets = [];
        foreach ($rows as $row) {
            $title = null;
            if (!empty($row['insertFlag']) && $row['insertFlag'] === 'true') {
                $title = $this->Titles->newEntity();
                $title->setCountry($countryId);
            } else if ($row['updateFlag'] === 'true') {
                $title = $this->Titles->get($row['titleId']);
            } else {
                continue;
            }

            // POSTされた値を設定
            $title->setFromArray($row);

            // バリデーションエラーの場合はそのまま返す
            if (($res = $this->Titles->validator()->errors($title->toArray()))) {
                // エラーメッセージを書き込み
                $this->Flash->error(__($this->_getErrorMessage($res)));
                // 検索結果表示処理へ
                // TODO: この場合再検索になるため入力値が消えるが、ビューにオブジェクトの一覧を返せない為止むを得ない
                return $this->search();
            }
            // 一覧に追加
            array_push($targets, $title);
        }
        return $targets;
    }
}
