<?php

namespace App\Controller;

use PDOException;
use Cake\Event\Event;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\TableRegistry;

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
        $this->_setTitle('タイトル情報検索');
        return $this->render('index');
    }

	/**
	 * 検索処理
	 */
	public function search()
    {
        // タイトル一覧を取得
        $this->request->data['is_search'] = true;
        if (!count($titles = $this->Titles->findTitlesByCountry($this->request->data))) {
            $this->Flash->info(__('検索結果が0件でした。'));
        }
        $this->set('titles', $titles);

        // 初期表示処理へ
        return $this->setAction('index');
	}

    /**
     * 登録・更新処理
     */
	public function saveAll()
    {
        // 更新対象が取得できなければ、検索結果表示処理へ
        if (!($targets = $this->__getUpdateTargets($this->request->data))) {
            // TODO: この場合再検索になるため入力値が消えるが、ビューにオブジェクトの一覧を返せない為止むを得ない
            return $this->setAction('search');
        }

        try {
			// 件数分処理
			foreach ($targets as $target) {
                // タイトル情報を更新
                $this->Titles->save($target);
			}
            $this->Flash->info(__(count($targets).'件のタイトルマスタを更新しました。'));
		} catch (PDOException $e) {
            $this->Log->error(__("タイトルマスタ登録・更新エラー：{$e->getMessage()}"));
			$this->Flash->error(__("タイトルマスタの更新に失敗しました…。"));
            $this->_markToRollback();
		} finally {
			// indexの処理を行う
			return $this->setAction('search');
		}
	}

	/**
	 * 詳細情報表示処理
     * 
     * @param $id 取得するデータのID
	 */
	public function detail($id = null)
    {
        // ダイアログ表示
        $this->_setDialogMode();

		// タイトル情報一式を設定
        if (!($title = $this->Titles->findTitleWithRelations($id))) {
            throw new NotFoundException(__("タイトル情報が取得できませんでした。ID：{$id}"));
        }
        $this->set('title', $title);

        return $this->render('detail');
    }

	/**
	 * タイトルマスタの更新処理
     * 
     * @param $id 保存対象データのID
	 */
	public function save($id)
    {
        // IDからデータを取得
        $title = $this->Titles->get($id);

        // バリデーションエラーの場合は詳細情報表示処理へ
        $data = $this->request->data;
        if (($errors = $this->Titles->validator()->errors($data))) {
            $this->Flash->error($errors);
            return $this->setAction('detail', $title->id);
        }

        // 入力値をエンティティに設定
        $this->Titles->patchEntity($title, $data);

        try {
            // 保存処理
            $this->Titles->save($title);
            $this->Flash->info(__("タイトル：{$title->name}を更新しました。"));
		} catch (PDOException $e) {
            $this->Log->error(__("タイトル更新エラー：{$e->getMessage()}"));
			$this->Flash->error(__("タイトルの更新に失敗しました…。"));
            $this->_markToRollback();
		} finally {
            // 詳細情報表示処理へ
            return $this->setAction('detail', $title->id);
		}
	}

	/**
	 * タイトル保持情報の登録
	 */
	public function addHistory()
    {
        $data = $this->request->data;
        $titleId = $this->request->data('title_id');

        // バリデーションエラーの場合はそのまま返す
        if (($errors = $this->RetentionHistories->validator()->errors($data))) {
            $this->Flash->error($errors);
            return $this->setTabAction('detail', 'histories', $titleId);
        }

        // すでに存在するかどうかを確認
		if ($this->RetentionHistories->findByKey($data)) {
            $this->Flash->error(__("タイトル保持情報がすでに存在します。タイトルID：{$titleId}"));
            return $this->setTabAction('detail', 'histories', $titleId);
		}

		try {
			// タイトル保持情報の登録
            $history = $this->RetentionHistories->newEntity($data);
			$this->RetentionHistories->save($history);

            $this->Flash->info(__("保持履歴を登録しました。"));
            // POSTされたデータを初期化
            $this->request->data = [];
		} catch (PDOException $e) {
            $this->Log->error(__("保持履歴登録エラー：{$e->getMessage()}"));
			$this->Flash->error(__("保持履歴の登録に失敗しました…。"));
            $this->_markToRollback();
		} finally {
            // 詳細情報表示処理へ
            return $this->setTabAction('detail', 'histories', $titleId);
		}
	}

    /**
     * 更新対象のタイトル一覧を取得します。
     * 
     * @param array $data
     * @return array
     */
    private function __getUpdateTargets(array $data)
    {
        // 登録 or 更新対象の一覧を生成
        $rows = $data['titles'];
        $targets = [];
        foreach ($rows as $row) {
            // 登録 or 更新対象外
            if (empty($row['is_save'])) {
                continue;
            }

            // IDがあれば更新、なければ登録
            if (!empty($row['id'])) {
                $title = $this->Titles->get($row['id']);
            } else {
                $title = $this->Titles->newEntity(['country_id' => $data['country_id']]);
            }

            // バリデーションエラーの場合は終了
            if (($errors = $this->Titles->validator()->errors($row))) {
                $this->Flash->error($errors);
                return null;
            }

            // POSTされた値を設定
            $this->Titles->patchEntity($title, $row);

            // 一覧に追加
            array_push($targets, $title);
        }
        return $targets;
    }
}
