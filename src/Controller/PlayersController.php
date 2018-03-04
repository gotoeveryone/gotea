<?php

namespace Gotea\Controller;

use Gotea\Form\PlayerForm;

/**
 * 棋士情報コントローラ
 *
 * @author  Kazuki Kamizuru
 * @since   2015/07/20
 *
 * @property \Gotea\Model\Table\PlayersTable $Players
 * @property \Gotea\Model\Table\CountriesTable $Countries
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

        $this->loadModel('Countries');
        $this->loadModel('Ranks');
        $this->loadModel('Organizations');
        $this->loadModel('TitleScores');
    }

    /**
     * 初期表示処理
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->set('form', new PlayerForm);

        return $this->withRanks()->renderWith('棋士情報検索');
    }

    /**
     * 検索処理
     *
     * @return \Cake\Http\Response|null
     */
    public function search()
    {
        $this->set('form', ($form = new PlayerForm));

        // バリデーション
        if (!$form->validate($this->request->getParsedBody())) {
            return $this->setErrors(400, $form->errors())->setAction('index');
        }

        // データを取得
        $players = $this->Players->findPlayers($this->request->getParsedBody());

        // 件数が0件または多すぎる場合はメッセージを出力
        $over = 300;
        if (!($count = $players->count())) {
            $this->Flash->warn(__('No matches found'));
        } elseif ($count > $over) {
            $this->Flash->warn(__(
                'Matched rows more than {0} ({1} row matched).<br/>Please filtering conditions and reexecute.',
                $over,
                $count
            ));
        } else {
            // 結果をセット
            $this->set(compact('players'));
        }

        return $this->withRanks()->renderWith('棋士情報検索', 'index');
    }

    /**
     * 新規作成画面表示処理
     *
     * @return \Cake\Http\Response|null
     */
    public function new()
    {
        $params = [];
        // 所属国
        if (($countryId = $this->request->getQuery('country_id'))) {
            $params['country_id'] = $countryId;

            if (is_numeric($countryId) && $this->Countries->exists(['id' => $countryId])) {
                // 所属組織
                $organization = $this->Organizations->findByCountryId($countryId)->first();
                if ($organization) {
                    $params['organization_id'] = $organization->id;
                }
            }
        }
        // 性別
        if (($sex = $this->request->getQuery('sex'))) {
            $params['sex'] = $sex;
        }
        // 入段日
        if (($joined = $this->request->getQuery('joined'))) {
            $params['joined'] = $joined;
        }
        $player = $this->Players->newEntity($params, [
            'validate' => false,
        ]);

        // 初段固定
        $player->rank = $this->Ranks->findByRankNumeric(1)->first();

        return $this->set(compact('player'))->withRanks()->renderWithDialog('view');
    }

    /**
     * 詳細表示処理
     *
     * @param int $id 取得するデータのID
     * @return \Cake\Http\Response|null
     */
    public function view(int $id)
    {
        $player = $this->Players->findByIdWithRelation($id);

        return $this->set(compact('player'))->withRanks()->renderWithDialog();
    }

    /**
     * 棋士の登録処理
     *
     * @return \Cake\Http\Response|null
     */
    public function create()
    {
        // エンティティ生成
        $data = $this->request->getData();
        $player = $this->Players->newEntity($data);

        // 失敗
        if (!$this->Players->save($player)) {
            $this->set(compact('player'));

            return $this->withRanks()->renderWithDialogErrors(400, $player->getErrors(), 'view');
        }

        $this->setMessages(__('The player {0} - {1} is saved', $player->id, $player->name));

        // 連続作成の場合は新規登録画面へリダイレクト
        if ($this->request->getData('is_continue')) {
            return $this->redirect([
                '_name' => 'new_player',
                '?' => [
                    'country_id' => $player->country_id,
                    'sex' => $player->sex,
                    'joined' => $player->joined,
                ],
            ]);
        }

        return $this->redirect(['_name' => 'view_player', $player->id]);
    }

    /**
     * 棋士の更新処理
     *
     * @param int $id 対象の棋士ID
     * @return \Cake\Http\Response|null
     */
    public function update(int $id)
    {
        // エンティティ取得
        $data = $this->request->getData();
        $player = $this->Players->findByIdWithRelation($id);
        $this->Players->patchEntity($player, $data);

        // 失敗
        if (!$this->Players->save($player)) {
            $this->set('player', $player);

            return $this->withRanks()->renderWithDialogErrors(400, $player->getErrors(), 'view');
        }

        $this->setMessages(__('The player {0} - {1} is saved', $player->id, $player->name));

        return $this->redirect(['_name' => 'view_player', $player->id]);
    }

    /**
     * ランキング出力処理
     *
     * @return \Cake\Http\Response|null
     */
    public function ranking()
    {
        return $this->renderWith('棋士勝敗ランキング出力');
    }

    /**
     * 段位一覧に表示する値を設定します。
     *
     * @return \Gotea\Controller\PlayersController
     */
    private function withRanks()
    {
        $ranks = $this->Ranks->findProfessional()->combine('id', 'name');

        return $this->set(compact('ranks'));
    }
}
