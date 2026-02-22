<?php
declare(strict_types=1);

namespace Gotea\Controller;

use Cake\Event\EventInterface;
use Cake\Http\Response;
use Gotea\Form\PlayerForm;
use Gotea\Model\Table\CountriesTable;
use Gotea\Model\Table\OrganizationsTable;
use Gotea\Model\Table\RanksTable;
use Gotea\Model\Table\TitleScoresTable;

/**
 * 棋士情報コントローラ
 *
 * @author  Kazuki Kamizuru
 * @since   2015/07/20
 * @property \Gotea\Model\Table\PlayersTable $Players
 * @property \Gotea\Model\Table\CountriesTable $Countries
 * @property \Gotea\Model\Table\RanksTable $Ranks
 * @property \Gotea\Model\Table\OrganizationsTable $Organizations
 * @property \Gotea\Model\Table\TitleScoresTable $TitleScores
 */
class PlayersController extends AppController
{
    protected CountriesTable $Countries;
    protected RanksTable $Ranks;
    protected OrganizationsTable $Organizations;
    protected TitleScoresTable $TitleScores;

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->Countries = $this->fetchTable('Countries');
        $this->Ranks = $this->fetchTable('Ranks');
        $this->Organizations = $this->fetchTable('Organizations');
        $this->TitleScores = $this->fetchTable('TitleScores');
    }

    /**
     * @inheritDoc
     */
    public function beforeFilter(EventInterface $event)
    {
        if ($this->request->getParam('action') !== 'index') {
            $this->Authorization->authorize($this->request, 'access');
        }

        parent::beforeFilter($event);
    }

    /**
     * 初期表示処理
     *
     * @return \Cake\Http\Response|null
     */
    public function index(): ?Response
    {
        $this->set('form', new PlayerForm());

        return $this->withRanks()->renderWith('棋士情報検索');
    }

    /**
     * 検索処理
     *
     * @return \Cake\Http\Response|null
     */
    public function search(): ?Response
    {
        $this->set('form', ($form = new PlayerForm()));

        // バリデーション
        $data = $this->getRequest()->getQueryParams();
        if (!$form->validate($data)) {
            return $this->withRanks()
                ->setErrors(400, $form->getErrors())
                ->render('index');
        }

        // データを取得
        $players = $this->paginate($this->Players->findPlayers($data));

        // 件数が0件の場合はメッセージを出力
        if (!$players->count()) {
            $this->Flash->warn(__('No matches found'));
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
    public function new(): ?Response
    {
        $params = [
            'rank_id' => $this->Ranks->findByRank()->id,
        ];
        // 所属国
        $countryId = $this->getRequest()->getQuery('country_id');
        if ($countryId) {
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
        $sex = $this->getRequest()->getQuery('sex');
        if ($sex) {
            $params['sex'] = $sex;
        }
        // 入段日
        $joinedYear = $this->getRequest()->getQuery('joined_year');
        if ($joinedYear) {
            $params['joined_year'] = $joinedYear;
        }
        $joinedMonth = $this->getRequest()->getQuery('joined_month');
        if ($joinedMonth) {
            $params['joined_month'] = $joinedMonth;
        }
        $joinedDay = $this->getRequest()->getQuery('joined_day');
        if ($joinedDay) {
            $params['joined_day'] = $joinedDay;
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
    public function view(int $id): ?Response
    {
        $player = $this->Players->findByIdWithRelation($id);

        return $this->set(compact('player'))->withRanks()->renderWithDialog();
    }

    /**
     * 棋士の登録処理
     *
     * @return \Cake\Http\Response|null
     */
    public function create(): ?Response
    {
        // エンティティ生成
        $data = $this->getRequest()->getParsedBody();
        $player = $this->Players->newEntity($data);

        // 失敗
        if (!$this->Players->save($player)) {
            $this->set(compact('player'));

            return $this->withRanks()->renderWithDialogErrors(400, $player->getErrors(), 'view');
        }

        $this->setMessages(__('The player {0} - {1} is saved', $player->id, $player->name));

        // 連続作成の場合は新規登録画面へリダイレクト
        if (!empty($data['is_continue'])) {
            return $this->redirect([
                '_name' => 'new_player',
                '?' => [
                    'country_id' => $player->country_id,
                    'sex' => $player->sex,
                    'joined_year' => $player->joined_year,
                    'joined_month' => $player->joined_month,
                    'joined_day' => $player->joined_day,
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
    public function update(int $id): ?Response
    {
        // エンティティ取得
        $data = $this->getRequest()->getParsedBody();
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
    public function ranking(): ?Response
    {
        return $this->renderWith('棋士勝敗ランキング出力');
    }

    /**
     * 段位一覧に表示する値を設定します。
     *
     * @return self
     */
    private function withRanks(): PlayersController
    {
        $ranks = $this->Ranks->findProfessional()->all()->combine('id', 'name');

        return $this->set(compact('ranks'));
    }
}
