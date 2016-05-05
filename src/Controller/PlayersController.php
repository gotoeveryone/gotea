<?php

namespace App\Controller;

use PDOException;
use Cake\Event\Event;
use Cake\Network\Exception\BadRequestException;
use Cake\ORM\TableRegistry;
use Cake\I18n\Time;
use Psr\Log\LogLevel;

/**
 * 棋士マスタ用コントローラ
 *
 * @author		Kazuki Kamizuru
 * @since		2015/07/20
 */
class PlayersController extends AppController
{
	/**
	 * 描画前処理
	 */
    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);

		// 段位プルダウン
        $ranks = TableRegistry::get('Ranks');
		$this->set('ranks', $ranks->getRanksToArray());
   	}

	/**
	 * 初期処理
	 */
	public function index()
    {
        $this->__initSearch();
        return $this->render('index');
    }

    /**
	 * 検索処理
	 */
	public function search()
    {
        $this->__initSearch();

        // リクエストから値を取得（なければセッションから取得）
        $countryCode = $this->_getParam('searchCountry');
        $sex = $this->_getParam('searchSex');
        $rank = $this->_getParam('searchRank');
        $playerName = $this->_getParam('searchPlayerName');
        $playerNameEn = $this->_getParam('searchPlayerNameEn');
        $enrollmentFrom = $this->_getParam('searchEnrollmentFrom');
        $enrollmentTo = $this->_getParam('searchEnrollmentTo');
        $retire = $this->_getParam('searchRetire');

        // 該当する棋士情報一覧の件数を取得
        $count = $this->Players->findPlayers($countryCode, $sex, $rank, $playerName, $playerNameEn,
                $enrollmentFrom, $enrollmentTo, $retire, true);

        // 件数が0件または1001件以上の場合はメッセージを出力（1001件以上の場合は一覧を表示しない）
        if (!$count) {
            $this->Flash->warn(__("検索結果が0件でした。"));
            $players = [];
        } else if ($count > 1000) {
            $this->Flash->warn(__("検索結果が1000件を超えています（{$count}件）。<BR>条件を絞って再検索してください。"));
            $players = [];
        } else {
            // 該当する棋士情報一覧を取得
            $players = $this->Players->findPlayers($countryCode, $sex, $rank, $playerName, $playerNameEn,
                    $enrollmentFrom, $enrollmentTo, $retire);
        }

        // 結果をセット
        $this->set('players', $players);

        // 値を格納
        $this->_setParam('searchCountry', $countryCode);
        $this->_setParam('searchRank', $rank);
        $this->_setParam('searchSex', $sex);
        $this->_setParam('searchPlayerName', $playerName);
        $this->_setParam('searchPlayerNameEn', $playerNameEn);
        $this->_setParam('searchEnrollmentFrom', $enrollmentFrom);
        $this->_setParam('searchEnrollmentTo', $enrollmentTo);
        $this->_setParam('searchRetire', $retire);

        // 初期表示処理へ
        return $this->render('index');
    }

	/**
	 * 棋士詳細表示処理
     * 
     * @param $id 棋士マスタのID
	 */
	public function detail($id = null)
    {
        $this->set('dialogFlag', true);

        // 棋士IDが取得出来なければ新規登録画面を表示
		if (!$id) {
            // 所属国、組織を取得
            $countryId = $this->request->query('countryId');
            $affiliation = $this->request->query('affiliation');

            // 所属国が取得出来なければエラー
            if (!$countryId) {
                throw new BadRequestException(__("所属国を指定してください。"));
            }

            $player = $this->Players->newEntity();

            // 所属国を設定
            $player->setCountry($countryId);

            $player->set('AFFILIATION', ($affiliation ? $affiliation : $player->country->NAME.'棋院'));
			$this->set('player', $player);

            return $this->render('detail');
		}

		// 棋士情報一式を取得
		$this->set('player', $this->Players->findPlayerAllRelations($id));

        return $this->render('detail');
	}

	/**
	 * 棋士マスタの更新
     * 
     * @param boolean $continue 連続作成するか
	 */
	public function save()
    {
        // IDからデータを取得
		$playerId = $this->request->data('selectPlayerId');
        $exist = ($playerId) ? true : false;
        $player = ($exist) ? $this->Players->get($playerId) : $this->Players->newEntity();
        $message = ($exist) ? '更新' : '登録';

        // 入力値をエンティティに設定
        $player->patchEntity($this->request);

        // バリデーションエラーの場合はそのまま返す
        $validator = $this->Players->validator('default');
        $res = $validator->errors($player->toArray());
        if ($res) {
            // エラーメッセージを書き込み
            $error = $this->_getErrorMessage($res);
            $this->Flash->error($error);
            // 詳細情報表示処理へ
			$this->request->query['countryId'] = $player->country->ID;
            return $this->detail($player->ID);
        }

        try {
            // 棋士マスタの保存
            $this->Players->save($player);
            $saveId = $player->ID;

            // 当年の棋士成績情報を取得
            $playerScores = TableRegistry::get('PlayerScores');
            $updateScore = $playerScores->findByPlayerAndYear($saveId, Time::now()->year);

            // 棋士マスタの段位と異なる場合は更新対象
			if ($player->rank->ID !== $updateScore->rank->ID) {
                $updateScore->setRank($player->rank->ID);
				$playerScores->save($updateScore);
			}

            // メッセージ出力
            $this->Flash->info(__("棋士ID：{$saveId}の棋士情報を{$message}しました。"));

		} catch (PDOException $e) {
            $this->log(__("棋士情報登録・更新エラー：{$e->getMessage()}"), LogLevel::ERROR);
			$this->isRollback = true;
			$this->Flash->error(__("棋士情報の{$message}に失敗しました…。"));
		} finally {
			// 所属国IDを設定
			$this->request->query['countryId'] = $player->country->ID;
            // 連続作成かどうか
            $continue = $this->request->data('isContinue');
            // 詳細情報表示処理へ
            return (!$continue) ? $this->detail($player->ID) : $this->detail();
		}
	}

	/**
	 * 棋士成績情報の更新
	 */
	public function updateScore()
    {
        // 棋士成績情報を取得
        $playerScores = TableRegistry::get('PlayerScores');
        $updateScore = $playerScores->get($this->request->data('selectScoreId'));

		// 入力値をエンティティに設定
        $updateScore->patchEntity($this->request);
        $targetYear = $updateScore->TARGET_YEAR;

        try {
			// 棋士成績情報の更新
			$playerScores->save($updateScore);
			$this->Flash->info(__("{$targetYear}年度の棋士成績情報を更新しました。"));
		} catch (PDOException $e) {
			$this->log(__("棋士成績情報登録・更新エラー：{$e->getMessage()}"), LogLevel::ERROR);
			$this->isRollback = true;
			$this->Flash->error(__("{$targetYear}年度の棋士成績情報の更新に失敗しました…。"));
		} finally {
            // 詳細情報表示処理へ
			return $this->detail($this->request->data('selectPlayerId'));
		}
	}

    /**
     * 検索画面初期処理
     */
    private function __initSearch()
    {
		// 所属国プルダウン
        $countries = TableRegistry::get('Countries');
		$this->set('countries', $countries->findCountryBelongToArray());
        $this->_setTitle('棋士情報検索');
    }
}
