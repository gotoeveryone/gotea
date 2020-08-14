<?php
declare(strict_types=1);

namespace Gotea\Controller\Api;

/**
 * API・段位コントローラ
 *
 * @property \Gotea\Model\Table\RanksTable $Ranks
 */
class RanksController extends ApiController
{
    /**
     * 段位一覧を取得します。
     *
     * @return \Cake\Http\Response 段位一覧
     */
    public function index()
    {
        $ranks = $this->Ranks->findProfessional();

        return $this->renderJson($ranks);
    }
}
