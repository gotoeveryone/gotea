<?php

namespace Gotea\Controller;

use Cake\Network\Exception\BadRequestException;
use Gotea\Form\PlayerForm;

/**
 * 棋士情報コントローラ
 *
 * @author  Kazuki Kamizuru
 * @since   2015/07/20
 *
 * @property \Gotea\Model\Table\PlayersTable $Players
 * @property \Gotea\Model\Table\OrganizationsTable $Organizations
 * @property \Gotea\Model\Table\TitleScoresTable $TitleScores
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
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->set('form', ($form = new PlayerForm));

        // 初期表示
        if (!$this->request->isPost()) {
            return $this->_renderWith('棋士情報検索', 'index');
        }

        // バリデーション
        if (!$form->validate($this->request->getParsedBody())) {
            return $this->_renderWithErrors($form->errors(), '棋士情報検索', 'index');
        }

        // データを取得
        $players = $this->Players->findPlayers($this->request);

        // 件数が0件または多すぎる場合はメッセージを出力
        $over = 300;
        if (!$players->count()) {
            $this->Flash->warn(__('検索結果が0件でした。'));
        } elseif (($count = $players->count()) > $over) {
            $warning = '検索結果が{0}件を超えています（{1}件）。<br/>条件を絞って再検索してください。';
            $this->Flash->warn(__($warning, $over, $count));
        } else {
            // 結果をセット
            $this->set(compact('players'));
        }

        return $this->_renderWith('棋士情報検索', 'index');
    }

    /**
     * 新規作成画面表示処理
     *
     * @return \Cake\Http\Response|null
     */
    public function new()
    {
        // セッションから棋士情報が取得できない場合はデフォルト値の表示
        if (!($player = $this->_consumeBySession('player'))) {
            // 所属国IDが取得出来なければエラー
            if (!($countryId = $this->request->getQuery('country_id'))) {
                throw new BadRequestException(__('所属国を指定してください。'));
            }

            // モデルを生成し、所属国と所属組織を設定
            $organization = $this->Organizations->findByCountryId($countryId)->firstOrFail();
            $player = $this->Players->newEntity([
                'country_id' => $countryId,
                'organization_id' => $organization->id,
            ]);
        }

        return $this->set('player', $player)->_renderWithDialog('detail');
    }

    /**
     * 詳細表示処理
     *
     * @param int $id 取得するデータのID
     * @return \Cake\Http\Response|null
     */
    public function detail(int $id)
    {
        // セッションから入力値が取得できなければIDで取得
        if (!($player = $this->_consumeBySession('player'))) {
            $player = $this->Players->get($id, [
                'contain' => [
                    'Countries',
                    'Ranks',
                    'TitleScoreDetails',
                    'PlayerRanks',
                    'PlayerRanks.Ranks',
                ],
            ]);
        }

        return $this->set(compact('player'))
            ->_renderWithDialog('detail');
    }

    /**
     * 棋士マスタの登録・更新処理
     *
     * @return \Cake\Http\Response|null
     */
    public function save()
    {
        // POST以外は許可しない
        $this->request->allowMethod(['post']);

        // エンティティ取得
        $id = $this->request->getData('id');
        $player = $this->Players->findOrNew(['id' => $id]);

        // 保存
        $this->Players->patchEntity($player, $this->request->getParsedBody());
        if (!$this->Players->save($player)) {
            $this->_writeToSession('player', $player)->_setErrors($player->errors());
        } else {
            $this->_setMessages(__('[{0}: {1}] を保存しました。', $player->id, $player->name));

            // 連続作成の場合は新規登録画面へリダイレクト
            if (!$id && $this->request->getData('is_continue')) {
                return $this->redirect([
                    'action' => 'new',
                    '?' => ['country_id' => $player->country_id],
                ]);
            }
        }

        return $this->setAction(($player->id ? 'detail' : 'new'), $player->id);
    }

    /**
     * ランキング出力処理
     *
     * @return \Cake\Http\Response|null
     */
    public function ranking()
    {
        return $this->_renderWith('棋士勝敗ランキング出力');
    }

    /**
     * 段位別棋士数表示
     *
     * @return \Cake\Http\Response|null
     */
    public function viewRanks()
    {
        return $this->_renderWith("段位別棋士数表示");
    }
}
