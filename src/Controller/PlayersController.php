<?php

namespace App\Controller;

use App\Form\PlayerForm;
use App\Model\Entity\Player;
use Cake\Event\Event;
use Cake\Network\Exception\BadRequestException;
use Cake\I18n\Date;

/**
 * 棋士マスタ用コントローラ
 *
 * @author  Kazuki Kamizuru
 * @since   2015/07/20
 * 
 * @property \App\Model\Table\PlayersTable $Players
 * @property \App\Model\Table\PlayerScoresTable $PlayerScores
 * @property \App\Model\Table\CountriesTable $Countries
 * @property \App\Model\Table\RanksTable $Ranks
 * @property \App\Model\Table\OrganizationsTable $Organizations
 */
class PlayersController extends AppController
{
    /**
     * 初期処理
     */
	public function initialize()
    {
        parent::initialize();

        // モデルをロード
        $this->loadModel('PlayerScores');
        $this->loadModel('Countries');
        $this->loadModel('Ranks');
        $this->loadModel('Organizations');

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
	public function index(PlayerForm $form = null)
    {
		// 所属国プルダウン
		$this->set('countries', $this->Countries->findCountryBelongToArray());
        $this->_setTitle('棋士情報検索');

        $this->set('form', ($form ? $form : new PlayerForm));
        return $this->render('index');
    }

    /**
	 * 検索処理
	 */
	public function search()
    {
        // リクエストから値を取得
        $data = $this->request->getParsedBody();
        $form = new PlayerForm();
        if (!$form->validate($data)) {
            $this->Flash->error($form->errors());
            return $this->setAction('index', $form);
        }

        // 該当する棋士情報一覧の件数を取得
        $players = $this->Players->findPlayers($data);

        // 件数が0件または1001件以上の場合はメッセージを出力（1001件以上の場合は一覧を表示しない）
        if (!($count = count($players))) {
            $this->Flash->warn(__("検索結果が0件でした。"));
        } else if ($count > 300) {
            $this->Flash->warn(__("検索結果が300件を超えています（{$count}件）。<BR>条件を絞って再検索してください。"));
        } else {
            // 結果をセット
            $this->set('players', $players);
        }

        // 初期表示処理へ
        return $this->setAction('index', $form);
    }

	/**
	 * 詳細情報表示処理
     * 
     * @param $id 取得するデータのID
     * @param $existPlayer 棋士情報（エラー時の遷移用）
	 */
	public function detail($id = null, Player $existPlayer = null)
    {
        // ダイアログ表示
        $this->_setDialogMode();

        // IDが指定されていれば、棋士情報一式を設定
        if ($id) {
            if (!($player = $this->Players->getInner($id))) {
                throw new NotFoundException(__("棋士情報が取得できませんでした。ID：{$id}"));
            }
            $this->set('player', $player);
            return $this->render('detail');
        }

        // 棋士情報があればそれをセットして描画
        if ($existPlayer) {
            $this->set('player', $existPlayer);
            return $this->render('detail');
        }

        // 棋士IDが取得出来なければ新規登録画面を表示
        // 所属国が取得出来なければエラー
        if (!($countryId = $this->request->getQuery('countryId'))) {
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
     * @param $id 保存対象データのID
	 */
	public function save($id = null)
    {
        // IDからデータを取得
        $player = ($id) ? $this->Players->findPlayerWithScores($id) : $this->Players->newEntity();
        $status = ($id) ? '更新' : '登録';

        // バリデーションエラーの場合は詳細情報表示処理へ
        $data = $this->request->getParsedBody();
        if (($errors = $this->Players->validator()->errors($this->request->getParsedBody()))) {
            $this->Flash->error($errors);
            return $this->setAction('detail', null, $player);
        }

        // 入力値をエンティティに設定
        $this->Players->patchEntity($player, $data);

        // 棋士成績を設定
        $this->__setPlayerScores($player);

        // 保存処理
        $this->Players->save($player);
        $this->Flash->info(__("棋士ID：{$player->id}の棋士情報を{$status}しました。"));

        // 所属国IDを設定
        $this->request->withQueryparams(['countryId' => $player->country_id]);

        // 詳細情報表示処理へ
        return ($this->request->getData('is_continue') ?
                $this->setAction('detail') : $this->setAction('detail', $player->id, $player));
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

        // バリデーションエラーの場合はそのまま返す
        $data = $this->request->getParsedBody();
        if (($errors = $this->PlayerScores->validator()->errors($data))) {
            // エラーメッセージを書き込み、詳細情報表示処理へ
            $this->Flash->error($errors);
            return $this->setTabAction('detail', 'scores', $score->player_id);
        }

        // 入力値をエンティティに設定
        $this->PlayerScores->patchEntity($score, $data);

        // 棋士成績情報の更新
        $this->PlayerScores->save($score);
        $this->Flash->info(__("{$score->target_year}年度の棋士成績情報を更新しました。"));

        // 詳細情報表示処理へ
        return $this->setTabAction('detail', 'scores', $score->player_id);
	}

    /**
     * ランキング出力処理
     */
    public function ranking()
    {
        // 所属国プルダウン
		$this->set('countries', $this->Countries->findCountryHasFileToArrayWithSuffix());
        // 年度プルダウン
        $years = [];
        for ($i = date('Y'); $i >= 2013; $i--) {
            $years[$i] = $i.'年度';
        }
        $this->set('years', $years);
        $this->_setTitle('棋士勝敗ランキング出力');
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
