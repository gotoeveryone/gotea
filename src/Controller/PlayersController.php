<?php

namespace App\Controller;

use PDOException;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
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
		$this->set('ranks', $ranks->find('list', [
            'keyField' => 'RANK',
            'valueField' => 'RANK_NAME'
        ])->where([
            'RANK !=' => '0'
        ])->order('RANK DESC')->toArray());
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
        $sex = $this->_getParam('searchSex');
        $rank = $this->_getParam('searchRank');
        $playerName = $this->_getParam('searchPlayerName');
        $playerNameEn = $this->_getParam('searchPlayerNameEn');
        $enrollmentFrom = $this->_getParam('searchEnrollmentFrom');
        $enrollmentTo = $this->_getParam('searchEnrollmentTo');
        $retire = $this->_getParam('searchRetire');

        // 棋士情報の取得
        $query = $this->Players->find();

        // 入力されたパラメータが空でなければ、WHERE句へ追加
        if (!empty($searchCountry)) {
            $query->where(['Players.COUNTRY_CD' => $searchCountry]);
        }
        if (!empty($sex)) {
            $query->where(['Players.SEX' => $sex]);
        }
        if (!empty($rank)) {
            $query->where(['Players.RANK' => $rank]);
        }
        if (!empty($playerName)) {
            $query->where(['Players.PLAYER_NAME LIKE' => '%'.$playerName.'%']);
        }
        if (!empty($playerNameEn)) {
            $query->where(['Players.PLAYER_NAME_EN LIKE' => '%'.$playerNameEn.'%']);
        }
        if (!empty($enrollmentFrom)) {
            $query->where(['Players.ENROLLMENT >=' => $enrollmentFrom]);
        }
        if (!empty($enrollmentTo)) {
            $query->where(['Players.ENROLLMENT <=' => $enrollmentTo]);
        }
        if (!empty($retire) && $retire === 'false') {
            $query->where(['Players.DELETE_FLAG' => 0]);
        }

        // データを取得
        $players = $query->order([
            'Players.RANK DESC',
            'Players.ENROLLMENT',
            'Players.ID'
        ])->contain([
            'PlayerScores' => function ($q) {
                return $q->where(['PlayerScores.TARGET_YEAR' => intval(Time::now()->year)]);
            },
            'Ranks',
            'Countries'
        ])->all();
//        $this->log($players, LogLevel::INFO);

        if (count($players) === 0) {
            $this->Flash->warn(__('検索結果が0件でした。'));
        } else if (count($players) > 1000) {
            $this->Flash->warn(__('検索結果が1000件を超えています。条件を絞って再検索してください。'));
            $players = [];
        }

        $this->set('players', $players);

        // 検索フラグを設定
		$this->set('searchFlag', true);

        // 値を格納
        $this->_setParam('searchCountry', $searchCountry);
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

        // 棋士IDが取得出来なければ新規登録画面を表示
		if (!$id) {
            $countries = TableRegistry::get('Countries');
            // 所属国を取得
			$country = $countries->get($this->_getParam('searchCountry'));
			$this->set('countryCd', $country->COUNTRY_CD);
			$this->set('countryName', $country->COUNTRY_NAME);

            $player = $this->Players->newEntity();
            $affiliation = $this->request->data('affiliation');
            $player->set('AFFILIATION', ($affiliation ? $affiliation : $country->COUNTRY_NAME.'棋院'));
			$this->set('player', $player);

            return $this->render('detail');
		}

		// 棋士情報一式を取得
		$player = $this->Players->find()->contain([
            'Countries',
            'Ranks',
            'PlayerScores' => function ($q) {
                return $q->order(['PlayerScores.TARGET_YEAR' => 'DESC']);
            },
            'PlayerScores.Ranks',
            'TitleRetains.Titles',
            'TitleRetains' => function ($q) {
                return $q->order([
                    'TitleRetains.TARGET_YEAR' => 'DESC',
                    'Titles.COUNTRY_CD' => 'ASC',
                    'Titles.SORT_ORDER' => 'ASC'
                ]);
            },
            'TitleRetains.Titles.Countries'
        ])->where(['Players.ID' => $id])->first();

//        $this->log($player, LogLevel::INFO);

        // 棋士の所属国を設定
		$this->set('countryCd', $player->country->COUNTRY_CD);
		$this->set('countryName', $player->country->COUNTRY_NAME);

		$this->set('player', $player);

        return $this->render('detail');
	}

	/**
	 * 棋士マスタの更新
	 */
	public function save()
    {
        // 必須カラムのフィールド
		$playerId = $this->request->data('selectPlayerId');
		$countryCd = $this->request->data('selectCountry');
		$rank = $this->request->data('rank');

		// NULL許可カラムのフィールド
		$playerNameEn = $this->request->data('playerNameEn');
		$playerNameOther = $this->request->data('playerNameOther');
		$enrollment = $this->request->data('enrollment');
		$birthday = $this->request->data('birthday');
		$affiliation = $this->request->data('affiliation');

		// データを取得
        $player = ($playerId) ? $this->Players->get($playerId) : $this->Players->newEntity();

        // 入力値を設定
        $player->set('PLAYER_NAME', $this->request->data('playerName'));
		$player->set('PLAYER_NAME_EN', (empty($playerNameEn) ? null : $playerNameEn));
		$player->set('PLAYER_NAME_OTHER', (empty($playerNameOther) ? null : $playerNameOther));
		$player->set('COUNTRY_CD', $countryCd);
		$player->set('RANK', $rank);
		$player->set('SEX', $this->request->data('sex'));
        $player->set('ENROLLMENT', (empty($enrollment) ? '' : str_replace('/', '', $enrollment)));
        $time = new Time();
        $player->set('BIRTHDAY', (empty($birthday) ? '' : $time->parseDate($birthday, 'YYYY/MM/dd')));
		$player->set('AFFILIATION', (empty($affiliation) ? null : $affiliation));
		$player->set('DELETE_FLAG', $this->request->data('retireFlag'));
        $player->set('PROCESSED_FLAG', 0);

        // バリデーションエラーの場合はそのまま返す
        $validator = $this->Players->validator('default');
        $res = $validator->errors($player->toArray());
        if ($res) {
            // エラーメッセージを書き込み
            $error = $this->_getErrorMessage($res);
            $this->Flash->error($error);
            // 詳細情報表示処理へ
//            return $this->render('detail');
            return $this->detail($playerId);
        }

        try {
            // 棋士マスタの保存
//            $this->log($player, LogLevel::INFO);
            $this->Players->save($player);

			// 状態によって棋士成績情報の登録・更新を制御
			$update = false;
			$thisYear = Time::now()->year;

            // 棋士成績情報
            $playerScores = TableRegistry::get('PlayerScores');

			if ($playerId) {
                // 当年の棋士成績情報を取得
				$updateScore = $playerScores->find()->where([
                    'PLAYER_ID' => $playerId,
                    'TARGET_YEAR' => $thisYear
				])->first();

                if (!$updateScore) {
                    $updateScore = $playerScores->newEntity();
                    $updateScore->set('PLAYER_ID', $playerId);
                    $updateScore->set('TARGET_YEAR', $thisYear);
                }

                // 棋士マスタの段位と異なる場合は更新対象
                $update = ($player->RANK !== $updateScore->PLAYER_RANK);
			}

			if ($update || !$playerId) {
				$updateScore->set('PLAYER_RANK', $rank);
				$updateScore->set('PROCESSED_FLAG', 0);
				$updateScore->set('DELETE_FLAG', 0);
				$playerScores->save($updateScore);
			}

            // メッセージ出力
            $this->Flash->info((!$playerId) ? '棋士ID：'.$playerId.'の棋士情報を登録しました。' : '棋士マスタを更新しました。');

		} catch (PDOException $e) {
			$this->log('棋士情報登録・更新エラー：'.$e->getMessage(), LogLevel::ERROR);
			$this->isRollback = true;
			$this->Flash->error('棋士情報の'.($playerId ? '更新' : '登録').'に失敗しました…。');
		} finally {
            // 詳細情報表示処理へ
			return $this->detail($playerId);
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
		$updateScore->set('WIN_POINT_WR', $this->request->data('selectWinPointWr'));
		$updateScore->set('LOSE_POINT_WR', $this->request->data('selectLosePointWr'));
		$updateScore->set('DRAW_POINT_WR', $this->request->data('selectDrawPointWr'));

		// トランザクションの開始
		$conn = ConnectionManager::get('default');
        $conn->begin();

        $selectYear = $this->request->data('selectYear');
		try {
			// 棋士成績情報の更新
			$updateScore->set('PROCESSED_FLAG', 0);
			$playerScores->save($updateScore);
			$conn->commit();
			$this->Flash->info($selectYear.'年度の棋士成績情報を更新しました。');
		} catch (PDOException $e) {
			$this->log('棋士成績情報登録・更新エラー：'.$e->getMessage());
			$conn->rollback();
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
		$this->set('countries', $countries->find('list', [
            'keyField' => 'COUNTRY_CD',
            'valueField' => 'COUNTRY_NAME'
        ])->where([
            'BELONG_FLAG ' => 1
        ])->toArray());

        $this->set('cakeDescription', '棋士情報検索');
    }
}
