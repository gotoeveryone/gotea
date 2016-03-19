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

        // 該当する棋士情報一覧を取得
        $players = $this->Players->findPlayers($countryCode, $sex, $rank, $playerName, $playerNameEn,
                $enrollmentFrom, $enrollmentTo, $retire);
//        $this->log($players, LogLevel::INFO);

        if (count($players) === 0) {
            $this->Flash->warn(__('検索結果が0件でした。'));
        } else if (count($players) > 1000) {
            $this->Flash->warn(__('検索結果が1000件を超えています。条件を絞って再検索してください。'));
            $players = [];
        }

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
		$this->set('cakeDescription', '棋士情報照会');
        $this->set('dialogFlag', true);

        // 棋士IDが取得出来なければ新規登録画面を表示
		if (!$id) {
            // 所属国、組織を取得
            $countryId = $this->request->query('countryId');
            $affiliation = $this->request->query('affiliation');

            // 所属国が取得出来なければエラー
            if (!$countryId) {
                throw new BadRequestException("所属国を指定してください。");
            }

            $player = $this->Players->newEntity();

            // 所属国を取得
            $countries = TableRegistry::get('Countries');
			$country = $countries->get($countryId);
            $player->set('country', $country);

            $player->set('AFFILIATION', ($affiliation ? $affiliation : $country->NAME.'棋院'));
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
        // 連続作成かどうか
		$continue = $this->request->data('isContinue');

        // 必須カラムのフィールド
		$playerId = $this->request->data('selectPlayerId');
		$countryId = $this->request->data('selectCountry');
		$rankId = $this->request->data('rank');

		// NULL許可カラムのフィールド
		$playerNameEn = $this->request->data('playerNameEn');
		$playerNameOther = $this->request->data('playerNameOther');
		$enrollment = $this->request->data('enrollment');
		$birthday = $this->request->data('birthday');
		$affiliation = $this->request->data('affiliation');

		// データを取得
        $player = ($playerId) ? $this->Players->get($playerId) : $this->Players->newEntity();

        // 入力値を設定
        $player->set('NAME', $this->request->data('playerName'));
		$player->set('NAME_ENGLISH', (empty($playerNameEn) ? null : $playerNameEn));
		$player->set('NAME_OTHER', (empty($playerNameOther) ? null : $playerNameOther));
		$player->set('COUNTRY_ID', $countryId);
		$player->set('RANK_ID', $rankId);
		$player->set('SEX', $this->request->data('sex'));
        $player->set('ENROLLMENT', (empty($enrollment) ? '' : str_replace('/', '', $enrollment)));
        $time = new Time();
        $player->set('BIRTHDAY', (empty($birthday) ? '' : $time->parseDate($birthday, 'YYYY/MM/dd')));
		$player->set('AFFILIATION', (empty($affiliation) ? null : $affiliation));
		$player->set('DELETE_FLAG', $this->request->data('retireFlag'));

        // バリデーションエラーの場合はそのまま返す
        $validator = $this->Players->validator('default');
        $res = $validator->errors($player->toArray());
        if ($res) {
            // エラーメッセージを書き込み
            $error = $this->_getErrorMessage($res);
            $this->Flash->error($error);
            // 詳細情報表示処理へ
			$this->request->query['countryId'] = $countryId;
            return $this->detail($playerId);
        }

        try {
            // 棋士マスタの保存
            $this->Players->save($player);

			// 状態によって棋士成績情報の登録・更新を制御
			$thisYear = Time::now()->year;

            // 棋士成績情報
            $playerScores = TableRegistry::get('PlayerScores');

            // 当年の棋士成績情報を取得
            $updateScore = $playerScores->findByPlayerAndYear($player->ID, $thisYear);

            // 棋士マスタの段位と異なる場合は更新対象
			if ($player->RANK_ID !== $updateScore->RANK_ID) {
				$updateScore->set('RANK_ID', $rankId);
				$playerScores->save($updateScore);
			}

            // メッセージ出力
            $this->Flash->info(__('棋士ID：'.$player->ID.'の棋士情報を'.($playerId ? '更新' : '登録').'しました。'));

		} catch (PDOException $e) {
			$this->log('棋士情報登録・更新エラー：'.$e->getMessage(), LogLevel::ERROR);
			$this->isRollback = true;
			$this->Flash->error('棋士情報の'.($playerId ? '更新' : '登録').'に失敗しました…。');
		} finally {
			// 所属国IDを設定
			$this->request->query['countryId'] = $countryId;
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

		// パラメータの設定
		$updateScore->set('WIN_POINT', $this->request->data('selectWinPoint'));
		$updateScore->set('LOSE_POINT', $this->request->data('selectLosePoint'));
		$updateScore->set('DRAW_POINT', $this->request->data('selectDrawPoint'));
		$updateScore->set('WIN_POINT_WORLD', $this->request->data('selectWinPointWr'));
		$updateScore->set('LOSE_POINT_WORLD', $this->request->data('selectLosePointWr'));
		$updateScore->set('DRAW_POINT_WORLD', $this->request->data('selectDrawPointWr'));

        $selectYear = $this->request->data('selectYear');
		try {
			// 棋士成績情報の更新
			$playerScores->save($updateScore);
			$this->Flash->info($selectYear.'年度の棋士成績情報を更新しました。');
		} catch (PDOException $e) {
			$this->log('棋士成績情報登録・更新エラー：'.$e->getMessage());
			$this->isRollback = true;
			$this->Flash->error($selectYear.'年度の棋士成績情報の更新に失敗しました…。');
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

        $this->set('cakeDescription', '棋士情報検索');
    }
}
