<?php
declare(strict_types=1);

namespace Gotea\Controller\Api;

use Cake\Http\Response;

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
    public function index(): Response
    {
        $ranks = $this->Ranks->findProfessional();

        return $this->renderJson($ranks);
    }
}
