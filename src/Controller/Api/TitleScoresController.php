<?php
declare(strict_types=1);

namespace Gotea\Controller\Api;

use Cake\Http\Response;

/**
 * TitleScores Controller
 *
 * @property \Gotea\Model\Table\TitleScoresTable $TitleScores
 * @property \Gotea\Model\Table\TitleScoreDetailsTable $TitleScoreDetails
 * @method \Gotea\Model\Entity\TitleScore[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TitleScoresController extends ApiController
{
    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->TitleScoreDetails = $this->fetchTable('TitleScoreDetails');

        $this->loadComponent('Paginator');
    }

    /**
     * View method
     *
     * @param int $id Title Score id.
     * @return \Cake\Http\Response Response json
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view(int $id): Response
    {
        $titleScore = $this->TitleScores->findByIdWithRelation($id);

        return $this->renderJson($titleScore->toArray());
    }

    /**
     * Edit method
     *
     * @param int $id Title Score id.
     * @return \Cake\Http\Response Response json
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit(int $id): Response
    {
        // 勝敗変更の場合は該当アクションを実施
        if ($this->getRequest()->getData('action') == 'switchDivision') {
            return $this->switchDivision($id);
        }

        // 開始日と同じにチェックがあった場合、終了日には開始日と同じ値をセットする
        $data = $this->request->getData();
        if ($this->request->getData('is_same_started')) {
            $data['ended'] = $data['started'];
        }

        // データ取得
        $titleScore = $this->TitleScores->findByIdWithRelation($id);
        $this->TitleScores->patchEntity($titleScore, $data);

        // 保存
        if (!$this->TitleScores->save($titleScore)) {
            return $this->renderError(400, $titleScore->getErrors());
        }

        return $this->renderJson($titleScore->toArray());
    }

    /**
     * 勝敗変更処理
     *
     * @param int $id 成績ID
     * @return \Cake\Http\Response Response json
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function switchDivision(int $id): Response
    {
        $titleScore = $this->TitleScores->get($id, [
            'contain' => 'TitleScoreDetails',
        ]);

        foreach ($titleScore->title_score_details as $detail) {
            switch ($detail->division) {
                case '勝':
                case '敗':
                    $detail->division = ($detail->division === '勝' ? '敗' : '勝');
                    $this->TitleScoreDetails->save($detail);
                    break;
                default:
                    break;
            }
        }

        return $this->renderJson($titleScore->toArray());
    }
}
