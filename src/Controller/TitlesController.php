<?php

namespace App\Controller;

use Cake\Event\Event;
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
	/**
	 * 描画前処理
	 */
    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);

		// 所属国プルダウン
        $countries = TableRegistry::get('Countries');
		$this->set('countries', $countries->findCountryHasFileToArray());
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

        // リクエストから値を取得（なければセッションから取得）
        $searchCountry = $this->_getParam('searchCountry');
        $searchDelete = $this->_getParam('searchDelete');

        // タイトル情報の取得
        $query = $this->Titles->find()->contain([
            'TitleRetains',
            'TitleRetains.Titles' => function ($q) {
                return $q->where(['TitleRetains.HOLDING = Titles.HOLDING']);
            },
            'TitleRetains.Players',
            'TitleRetains.Ranks'
        ])->where([
            'Titles.COUNTRY_CD' => $searchCountry
        ]);

        // 入力されたパラメータが空でなければ、WHERE句へ追加
        if (!empty($searchDelete) && $searchDelete === 'false') {
            $query->where(['Titles.DELETE_FLAG' => 0]);
        }

        // データを取得
        $titles = $query->order(['Titles.SORT_ORDER' => 'ASC'])->all();
//        $this->log($titles, LogLevel::INFO);

        if (count($titles) === 0) {
            $this->Flash->info('検索結果が0件でした。');
        }

        $this->set('titles', $titles);

        // 検索フラグを設定
		$this->set('searchFlag', true);

        // 値を格納
        $this->_setParam('searchCountry', $searchCountry);
        $this->_setParam('searchDelete', $searchDelete);

        // indexページへ描画
        return $this->render('index');
	}

    /**
     * 登録・更新処理
     */
	public function save()
    {
        // POSTされたタイトル情報を取得
        $rows = $this->request->data('titles');

        // パラメータを取得
        $countryCd = $this->request->data('searchCountry');

        // 更新件数
        $count = 0;

        // 登録 or 更新対象の一覧を生成
        $targets = [];
        foreach ($rows as $row) {
            $title = null;
            if (!empty($row['insertFlag']) && $row['insertFlag'] === 'true') {
                $title = $this->Titles->newEntity();
                $title->set('COUNTRY_CD', $countryCd);
            } else if ($row['updateFlag'] === 'true') {
                $title = $this->Titles->get($row['titleId']);
            } else {
                continue;
            }

            // POSTされた値を設定
            $title->set('TITLE_NAME', $row['titleName']);
            $title->set('TITLE_NAME_EN', $row['titleNameEn']);
            $title->set('HOLDING', $row['holding']);
            $title->set('SORT_ORDER', $row['order']);
            $title->set('GROUP_FLAG', $row['groupFlag']);
            $title->set('HTML_FILE_NAME', $row['htmlFileName']);
            $title->set('HTML_MODIFY_DATE', date($row['htmlModifyDate']));
            $title->set('PROCESSED_FLAG', 0);
            $title->set('DELETE_FLAG', $row['deleteFlag']);

            // バリデーションエラーの場合はそのまま返す
            $validator = $this->Titles->validator('default');
            $res = $validator->errors($title->toArray());
            if ($res) {
                // エラーメッセージを書き込み
                $error = $this->_getErrorMessage($res);
                $this->Flash->error($error);
                // 検索結果表示処理へ
                // TODO: この場合再検索になるため入力値が消えるが、ビューにオブジェクトの一覧を返せない為止むを得ない
                return $this->search();
            }
            // 一覧に追加
            array_push($targets, $title);
        }

        try {
			// 件数分処理
			foreach ($targets as $target) {
                // タイトル情報を更新
                $this->Titles->save($target);
                $count++;
			}
			$this->Flash->info($count.'件のタイトルマスタを更新しました。');
		} catch (PDOException $e) {
			$this->log('タイトルマスタ登録・更新エラー：'.$e->getMessage(), LogLevel::ERROR);
			$this->isrollback = true;
			$this->Flash->error('タイトルマスタの更新に失敗しました…。');
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
		$this->set('cakeDescription', 'タイトル情報照会・修正');
        $this->set('dialogFlag', true);

		// タイトル情報一式を設定
        $this->set('title', $this->Titles->findTitleAllRelations($id));

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
        $this->log('期：'.$holding, LogLevel::INFO);

        $titleRetains = TableRegistry::get('TitleRetains');

        // すでに存在するかどうかを確認
		$exist = $titleRetains->find()->where([
            'TitleRetains.TITLE_ID' => $titleId,
            'TitleRetains.HOLDING' => $holding
		])->count();
        $this->log('件数：'.$exist, LogLevel::INFO);

		if ($exist) {
			$this->Flash->error('タイトル保持情報がすでに存在します。');
			return $this->detail($titleId);
		}

		// NULL許可カラムのフィールド
		$playerId = $this->request->data('registPlayerId');
		$playerRank = $this->request->data('registRank');
		$winGroupName = $this->request->data('registGroupName');

        // エンティティを新規作成
        $titleRetain = $titleRetains->newEntity();

		$titleRetain->set('TITLE_ID', $this->request->data('selectTitleId'));
		$titleRetain->set('HOLDING', $holding);
		$titleRetain->set('TARGET_YEAR', $this->request->data('registYear'));
		if (!empty($playerId)) {
			$titleRetain->set('PLAYER_ID', $playerId);
		}
		if (!empty($playerRank)) {
			$titleRetain->set('PLAYER_RANK', $playerRank);
		}
		if (!empty($winGroupName)) {
			$titleRetain->set('WIN_GROUP_NAME', $winGroupName);
		}
        $titleRetain->set('PROCESSED_FLAG', 0);
        $titleRetain->set('DELETE_FLAG', 0);

        // バリデーションエラーの場合はそのまま返す
        $validator = $titleRetains->validator('default');
        $res = $validator->errors($titleRetain->toArray());
        if ($res) {
            // エラーメッセージを書き込み、詳細情報表示処理へ
            $this->Flash->error($this->_getErrorMessage($res));
            return $this->detail($titleId);
        }

		try {
			// タイトル保持情報の保存
			$titleRetains->save($titleRetain);
			$this->Flash->info('タイトル保持情報を登録しました。');
		} catch (PDOException $e) {
			$this->log('タイトル保持情報登録・更新エラー：'.$e->getMessage());
			$this->isRollback = true;
			$this->Flash->error('タイトル保持情報の登録に失敗しました…。');
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
        $this->set('cakeDescription', 'タイトル情報検索');
    }
}
