<?php

namespace App\Controller;

use Cake\Network\Exception\BadRequestException;
use Cake\I18n\Date;
use App\Form\PlayerForm;

/**
 * 棋士情報コントローラ
 *
 * @author  Kazuki Kamizuru
 * @since   2015/07/20
 *
 * @property \App\Model\Table\PlayersTable $Players
 * @property \App\Model\Table\OrganizationsTable $Organizations
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

        $this->loadModel('Organizations');
        $this->loadModel('TitleScores');
    }

    /**
     * 初期表示・検索処理
     *
     * @return \Psr\Http\Message\ResponseInterface
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
            } elseif ($count > 300) {
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
     * 新規作成画面表示処理
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function new()
    {
        // ダイアログ表示
        $this->_setDialogMode();

        if (!($player = $this->__readSession())) {
            // 所属国IDが取得出来なければエラー
            if (!($countryId = $this->request->getQuery('country_id'))) {
                throw new BadRequestException(__("所属国を指定してください。"));
            }

            // モデルを生成し、所属国と所属組織を設定
            $organization = $this->Organizations->findByCountryId($countryId)->firstOrFail();
            $player = $this->Players->newEntity([
                'country_id' => $countryId,
                'organization_id' => $organization->id,
            ]);
        }

        return $this->set('player', $player)->render('detail');
    }

    /**
     * 詳細表示処理
     *
     * @param int $id 取得するデータのID
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function detail(int $id)
    {
        // セッションから入力値が取得できなければIDで取得
        if (!($player = $this->__readSession())) {
            $player = $this->Players->get($id);
        }
        $scores = $this->TitleScores->findFromYear($player->id, Date::now()->year);

        return $this->_setDialogMode()
            ->set('player', $player)->set('scores', $scores)->render('detail');
    }

    /**
     * 棋士マスタの登録・更新処理
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function save()
    {
        // POST以外は許可しない
        $this->request->allowMethod(['post']);

        // エンティティ取得
        $id = $this->request->getData('id');
        $player = ($id) ? $this->Players->get($id) : $this->Players->newEntity();

        // 保存
        $this->Players->patchEntity($player, $this->request->getParsedBody());
        if (!$this->Players->save($player)) {
            $this->__writeSession($player)->_setErrors($player->errors());
        } else {
            $this->_setMessages(__("ID：{$player->id}の棋士情報を保存しました。"));

            // 連続作成の場合は新規登録画面へリダイレクト
            if (!$id && ($continue = $this->request->getData('is_continue'))) {
                return $this->redirect([
                    'action' => 'new',
                    '?' => ['country_id' => $player->country_id],
                ]);
            }
        }

        return $this->setAction(($id ? 'detail' : 'new'), $id);
    }

    /**
     * ランキング出力処理
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function ranking()
    {
        return $this->_setTitle('棋士勝敗ランキング出力')->render();
    }

    /**
     * 段位別棋士数表示
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function viewRanks()
    {
        return $this->_setTitle("段位別棋士数表示")->render();
    }

    /**
     * 入力値をセッションに設定します。
     *
     * @param \App\Model\Entity\Player $player
     * @return \Cake\Controller\Controller
     */
    private function __writeSession($player)
    {
        $this->request->session()->write('player', $player);
        return $this;
    }

    /**
     * 入力値をセッションから取得します。
     *
     * @return \App\Model\Entity\Player|null
     */
    private function __readSession()
    {
        return $this->request->session()->consume('player');
    }
}
