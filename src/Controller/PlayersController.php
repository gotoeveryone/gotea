<?php

namespace App\Controller;

use PDOException;
use App\Model\Entity\Player;
use Cake\Event\Event;
use Cake\Network\Exception\BadRequestException;
use Cake\ORM\TableRegistry;
use Cake\I18n\Date;
use Psr\Log\LogLevel;

/**
 * 棋士マスタ用コントローラ
 *
 * @author		Kazuki Kamizuru
 * @since		2015/07/20
 */
class PlayersController extends AppController
{
    // アソシエーション先のモデル群
    private $PlayerScores = null;
    private $Countries = null;
    private $Ranks = null;
    private $Organizations = null;

    /**
     * 初期処理
     */
	public function initialize()
    {
        parent::initialize();
        $this->PlayerScores = TableRegistry::get('PlayerScores');
        $this->Countries = TableRegistry::get('Countries');
        $this->Ranks = TableRegistry::get('Ranks');
        $this->Organizations = TableRegistry::get('Organizations');

        // GETアクセスを許可するアクションを定義
        $this->_addAllowGetActions(["categorize", "ranking"]);
    }

	/**
	 * 描画前処理
	 */
    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);

		// 段位プルダウン
		$this->set('ranks', $this->Ranks->getRanksToArray());
		// 所属プルダウン
		$this->set('organizations', $this->Organizations->findToKeyValue());
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

        // リクエストから値を取得
        $searchPlayer = $this->Players->newEntity([
            "country_id" => $this->request->data('searchCountry'),
            "organization_id" => $this->request->data('searchOrganization'),
            "rank_id" => $this->request->data('searchRank'),
            "sex" => $this->request->data('searchSex'),
            "name" => $this->request->data('searchPlayerName'),
            "name_english" => $this->request->data('searchPlayerNameEn'),
            "is_retired" => $this->request->data('searchRetire')
        ]);

        $joinedFrom = $this->request->data('searchJoinedFrom');
        $joinedTo = $this->request->data('searchJoinedTo');

        // 該当する棋士情報一覧の件数を取得
        $players = $this->Players->findPlayers($searchPlayer, $joinedFrom, $joinedTo);

        // 件数が0件または1001件以上の場合はメッセージを出力（1001件以上の場合は一覧を表示しない）
        if (!($count = count($players))) {
            $this->Flash->warn(__("検索結果が0件でした。"));
        } else if ($count > 1000) {
            $this->Flash->warn(__("検索結果が1000件を超えています（{$count}件）。<BR>条件を絞って再検索してください。"));
        } else {
            // 結果をセット
            $this->set('players', $players);
        }

        // 初期表示処理へ
        return $this->render('index');
    }

	/**
	 * 棋士詳細表示処理
     * 
     * @param $id 棋士マスタのID
     * @param $existPlayer 棋士情報（エラー時の遷移用）
	 */
	public function detail($id = null, Player $existPlayer = null)
    {
        // ダイアログ表示
        $this->_setDialogMode();

        // 棋士IDがあればデータを取得して表示
        if ($id) {
            // 棋士情報一式を取得
            $this->set('player', $this->Players->get($id));
            return $this->render('detail');
        }

        // 棋士情報があればそれをセットして描画
        if ($existPlayer) {
            $this->set('player', $existPlayer);
            return $this->render('detail');
        }

        // 棋士IDが取得出来なければ新規登録画面を表示
        // 所属国が取得出来なければエラー
        if (!($countryId = $this->request->query('countryId'))) {
            throw new BadRequestException(__("所属国を指定してください。"));
        }

        // モデルを生成し、国と組織を設定
        $player = $this->Players->newEntity(['country_id' => $countryId]);
        $player->organization = $this->Organizations->findByCountry($countryId)->first();

        $this->set('player', $player);
        return $this->render('detail');
	}

	/**
	 * 棋士マスタの登録・更新処理
     * 
     * @param $id 棋士マスタのID
	 */
	public function save($id = null)
    {
        // IDからデータを取得
        $player = ($id) ? $this->Players->findPlayerWithScores($id) : $this->Players->newEntity();
        $status = ($id) ? '更新' : '登録';

        // 入力値をエンティティに設定
        $this->Players->patchEntity($player, $this->request->data, ['validate' => false]);

        // バリデーションエラーの場合はそのまま返す
        if (($errors = $this->Players->validator()->errors($player->toArray()))) {
            // エラーメッセージを書き込み、詳細情報表示処理へ
            $this->Flash->error(__($this->_getErrorMessage($errors)));
            return $this->detail(null, $player);
        }

        // 棋士成績を設定
        $this->__setPlayerScores($player);

        try {
            // 保存処理
            $this->Players->save($player);
            $this->Flash->info(__("棋士ID：{$player->id}の棋士情報を{$status}しました。"));
		} catch (PDOException $e) {
            $this->Log->error(__("棋士情報{$status}エラー：{$e->getMessage()}"));
            $this->_markToRollback();
			$this->Flash->error(__("棋士情報の{$status}に失敗しました…。"));
		} finally {
			// 所属国IDを設定
			$this->request->query['countryId'] = $player->country_id;
            // 詳細情報表示処理へ
            return ($this->request->data('isContinue') === "true") ? $this->detail() : $this->detail($player->id, $player);
		}
	}

	/**
	 * 棋士成績を更新します。
     * 
     * @param $id 棋士成績のID
	 */
	public function saveScore($id)
    {
        // IDからデータを取得
        $score = $this->PlayerScores->get($id);

        // 入力値をエンティティに設定
        $this->PlayerScores->patchEntity($score, $this->request->data, ['validate' => false]);

        // バリデーションエラーの場合はそのまま返す
        if (($errors = $this->PlayerScores->validator()->errors($score->toArray()))) {
            // エラーメッセージを書き込み、詳細情報表示処理へ
            $this->Flash->error(__($this->_getErrorMessage($errors)));
            return $this->detail($score->player_id);
        }

        try {
			// 棋士成績情報の更新
			$this->PlayerScores->save($score);
			$this->Flash->info(__("{$score->target_year}年度の棋士成績情報を更新しました。"));
		} catch (PDOException $e) {
			$this->log(__("棋士成績情報更新エラー：{$e->getMessage()}"), LogLevel::ERROR);
            $this->_markToRollback();
			$this->Flash->error(__("{$score->target_year}年度の棋士成績情報の更新に失敗しました…。"));
		} finally {
            // 詳細情報表示処理へ
			return $this->detail($score->player_id);
		}
	}

    /**
     * ランキング出力処理
     */
    public function ranking()
    {
        $this->loadComponent('Json');

        $this->_setTitle('棋士勝敗ランキング出力');

        // 所属国プルダウン
		$this->set('countries', $this->Countries->findCountryHasFileToArrayWithSuffix());

        // 年度プルダウン
        $this->set('years', $this->PlayerScores->findScoreUpdateToArrayWithSuffix());
    }

    /**
     * カテゴリ処理
     */
    public function categorize()
    {
		// 所属国プルダウン
		$this->set("countries", $this->Countries->findCountryBelongToArray());
        $this->_setTitle("段位別棋士数検索");
    }

    /**
     * 検索画面初期処理
     */
    private function __initSearch()
    {
		// 所属国プルダウン
		$this->set('countries', $this->Countries->findCountryBelongToArray());
        $this->_setTitle('棋士情報検索');
    }

    /**
     * 棋士成績データを設定します。
     * 
     * @param type $player
     */
    private function __setPlayerScores(&$player)
    {
        // 新規登録時は棋士成績を設定
        $now_year = Date::now()->year;
        if (!$player->id) {
            $score = $this->PlayerScores->newEntity([
                'target_year' => $now_year,
                'rank_id' => $player->rank_id
            ]);
            $player->set('player_scores', [$score]);
        } else {
            // 当年のデータで段位が異なる場合は更新
            foreach ($player->player_scores as $score) {
                if ($score->target_year === $now_year
                        && $player->rank_id !== $score->rank_id) {
                    $score->rank_id = $player->rank_id;
                    break;
                }
            }
            $player->set('player_scores', $player->player_scores);
        }
    }
}
