<?php

namespace App\Controller;

use App\Form\PlayerForm;
use App\Model\Entity\Player;
use Cake\Event\Event;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\NotFoundException;
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
     * {@inheritDoc}
     */
	public function initialize()
    {
        parent::initialize();

        // モデルをロード
        $this->loadModel('PlayerScores');
        $this->loadModel('Countries');
        $this->loadModel('Ranks');
        $this->loadModel('Organizations');
    }

	/**
     * {@inheritDoc}
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
	 * 初期処理 or 検索処理
     *
     * @return Response
	 */
	public function index()
    {
        $this->_setTitle('棋士情報検索');
		// 所属国プルダウン
		$this->set('countries', $this->Countries->findCountryBelongToArray());

        // 検索
        if ($this->request->isPost()) {
            // リクエストから値を取得
            $data = $this->request->getParsedBody();
            $form = new PlayerForm();
            if (!$form->validate($data)) {
                $this->Flash->error($form->errors());
                return $this->setAction('index', $form);
            }

            // 該当する棋士情報一覧の件数を取得
            $query = $this->Players->findPlayersQuery($data);
            $count = $query->count();

            // 件数が0件または301件以上の場合はメッセージを出力（1001件以上の場合は一覧を表示しない）
            if ($count === 0) {
                $this->Flash->warn(__("検索結果が0件でした。"));
            } else if ($count > 300) {
                $this->Flash->warn(__("検索結果が300件を超えています（{$count}件）。<BR>条件を絞って再検索してください。"));
            } else {
                // 結果をセット
                $this->set('players', $query->all());
            }
        }

        $this->set('form', ($form ?? new PlayerForm));
        return $this->render('index');
    }

	/**
	 * 詳細情報表示処理
     *
     * @param int|null $id 取得するデータのID
     * @param Player $existPlayer 棋士情報（エラー時の遷移用）
     * @return Response
	 */
	public function detail($id = null, Player $existPlayer = null)
    {
        // ダイアログ表示
        $this->_setDialogMode();

        // IDが指定されていれば、棋士情報一式を設定
        if ($id) {
            if (!($player = $this->Players->findWithRelations($id))) {
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

        // 棋士ID・既存の棋士情報が取得出来なければ新規登録画面を表示
        // 所属国が取得出来なければエラー
        if (!($countryId = $this->request->getQuery('country_id'))) {
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
     * @param int|null $id 保存対象データのID
     * @return Response
	 */
	public function save($id = null)
    {
        // POST以外は許可しない
        $this->request->allowMethod(['post']);

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

        // 保存処理
        $this->Players->save($player);
        $this->Flash->info(__("棋士ID：{$player->id}の棋士情報を{$status}しました。"));

        // 連続作成なら値を消す
        if (($continue = $this->request->getData('is_continue'))) {
            $this->request = $this->request->withParsedBody([]);
        }

        // 所属国IDを設定
        $this->request = $this->request->withQueryparams([
            'country_id' => $player->country_id
        ]);

        // 詳細情報表示処理へ
        return ($continue ? $this->setAction('detail')
                : $this->setAction('detail', $player->id, $player));
	}

    /**
     * ランキング出力処理
     *
     * @return Response
     */
    public function ranking()
    {
        $this->_setTitle('棋士勝敗ランキング出力');
        return $this->render();
    }

    /**
     * 段位別棋士数表示
     *
     * @return Response
     */
    public function viewRanks()
    {
        $this->_setTitle("段位別棋士数表示");
        return $this->render();
    }
}
