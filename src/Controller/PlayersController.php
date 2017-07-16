<?php

namespace App\Controller;

use PDOException;
use Cake\Event\Event;
use Cake\Network\Exception\BadRequestException;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\I18n\Date;
use App\Form\PlayerForm;
use App\Model\Entity\Player;

/**
 * 棋士マスタ用コントローラ
 *
 * @author  Kazuki Kamizuru
 * @since   2015/07/20
 *
 * @property \App\Model\Table\PlayersTable $Players
 * @property \App\Model\Table\PlayerRanksTable $PlayerRanks
 * @property \App\Model\Table\TitleScoresTable $TitleScores
 */
class PlayersController extends AppController
{
    /**
     * {@inheritDoc}
     */
	public function initialize()
    {
        parent::initialize();

        // モデルをロード
        $this->loadModel('PlayerRanks');
        $this->loadModel('TitleScores');
    }

	/**
	 * 初期処理 or 検索処理
     *
     * @return Response
	 */
	public function index()
    {
        $this->_setTitle('棋士情報検索');

        // 検索
        if ($this->request->isPost()) {
            // リクエストから値を取得
            $form = new PlayerForm();
            if (!$form->validate($this->request->getParsedBody())) {
                return $this->_setErrors($form->errors())
                    ->set('form', $form)->render('index');
            }

            // 該当する棋士情報一覧の件数を取得
            $query = $this->Players->findPlayersQuery($this->request);

            // 件数が0件または301件以上の場合はメッセージを出力（1001件以上の場合は一覧を表示しない）
            if (($count = $query->count()) === 0) {
                $this->Flash->warn(__("検索結果が0件でした。"));
            } else if ($count > 300) {
                $this->Flash->warn(__("検索結果が300件を超えています（{$count}件）。<BR>条件を絞って再検索してください。"));
            } else {
                // 結果をセット
                $players = $query->all();
                $this->set('players', $players)
                    ->set('scores', $this->TitleScores->findFromYear(
                        $players->extract('id')->toArray(), Date::now()->year));
            }
        }

        return $this->set('form', ($form ?? new PlayerForm))->render('index');
    }

	/**
	 * 詳細情報表示処理
     *
     * @param int|null $id 取得するデータのID
     * @param Player $player 棋士情報（エラー時の遷移用）
     * @return Response
	 */
	public function detail($id = null, $player = null)
    {
        // ダイアログ表示
        $this->_setDialogMode();

        if ($id && !($player = $this->Players->get($id))) {
            throw new RecordNotFoundException(__("棋士情報が取得できませんでした。ID：{$id}"));
        }

        // 棋士ID・既存の棋士情報が取得出来なければ新規登録画面を表示
        if (!$player) {
            // 所属国が取得出来なければエラー
            if (!($countryId = $this->request->getQuery('country_id'))) {
                throw new BadRequestException(__("所属国を指定してください。"));
            }

            // モデルを生成し、国と組織を設定
            $player = $this->Players->newEntity([
                'country_id' => $countryId,
            ]);
            $player->organization_id = $player->country->organizations->first()->id;
        }

        return $this->set('player', $player)
            ->set('scores', $this->TitleScores->findFromYear(
                $player->id, Date::now()->year))
            ->render('detail');
	}

	/**
	 * 棋士マスタの登録・更新処理
     *
     * @param int|null $id 保存対象データのID
     * @return Response
	 */
	public function save()
    {
        // POST以外は許可しない
        $this->request->allowMethod(['post']);

        // エンティティ取得
        $id = $this->request->getData('id');
        $player = ($id) ? $this->Players->get($id) : $this->Players->newEntity();

        // バリデーション
        $this->Players->patchEntity($player, $this->request->getParsedBody());
        if (($errors = $player->errors())) {
            return $this->_setErrors($errors)
                ->setAction('detail', null, $player);
        }

        // 新規登録でない場合は保存して終了
        if ($id) {
            $this->Players->save($player);
            return $this->_setMessages(__("棋士ID：{$player->id}の棋士情報を更新しました。"))
                ->setAction('detail', $player->id);
        }

        // 以降は新規登録時の処理
        return $this->addPlayer($player);
	}

    /**
     * 昇段情報追加
     *
     * @return Response
     */
    public function addRanks()
    {
        // POST以外は許可しない
        $this->request->allowMethod(['post']);

        // タイトルIDが取得できなければエラー
        if (!($id = $this->request->getData('player_id'))) {
            throw new BadRequestException(__('棋士IDは必須です。'));
        }

        // バリデーション
        $playerRanks = $this->PlayerRanks->newEntity($this->request->getParsedBody());
        if (($errors = $playerRanks->errors())) {
            return $this->_setErrors($errors)
                ->setTabAction('detail', 'ranks', $id);
        }

        // すでに存在するかどうかを確認
		if (!$this->PlayerRanks->add($playerRanks->toArray())) {
            return $this->_setErrors(__('昇段情報がすでに存在します。'))
                ->setTabAction('detail', 'ranks', $id);
		}

        // 最新データとして指定があれば棋士情報を更新
        if ($this->request->getData('newest')) {
            $player = $this->Players->get($id);
            $player->rank_id = $playerRanks->rank_id;
            $this->Players->save($player);
        }

        // リクエストを初期化して詳細画面に遷移
        return $this->_setMessages(__("昇段情報を登録しました。"))
            ->_resetRequest()
            ->setTabAction('detail', 'ranks', $id);
    }

    /**
     * ランキング出力処理
     *
     * @return Response
     */
    public function ranking()
    {
        return $this->_setTitle('棋士勝敗ランキング出力')->render();
    }

    /**
     * 段位別棋士数表示
     *
     * @return Response
     */
    public function viewRanks()
    {
        return $this->_setTitle("段位別棋士数表示")->render();
    }

    /**
     * 段位別棋士数表示
     *
     * @param Player $player
     * @return Response
     */
    private function addPlayer(Player $player)
    {
        // 登録処理
		if (!($player = $this->Players->add($player->toArray()))) {
            return $this->_setErrors(__('棋士情報がすでに存在します。'))
                ->setAction('detail', null, $player);
		}

        // 入段日を登録時段位の昇段日として設定
        $promoted = Date::parseDate($player->joined, 'yyyyMMdd');

        // 棋士昇段情報へ登録
        if (!$this->PlayerRanks->add([
            'player_id' => $player->id,
            'rank_id' => $player->rank_id,
            'promoted' => $promoted->format('Y/m/d'),
        ])) {
            throw new PDOException('棋士昇段情報への登録に失敗しました。');
        }

        // 連続作成ならリクエストを初期化
        if (($continue = $this->request->getData('is_continue'))) {
            $this->_resetRequest();
        }

        // 所属国IDを設定
        $this->request = $this->request->withQueryparams([
            'country_id' => $player->country_id
        ]);

        // 詳細情報表示処理へ
        return $this->_setMessages(__("棋士ID：{$player->id}の棋士情報を登録しました。"))
            ->setAction('detail', ($continue ? null : $player->id));
    }
}
