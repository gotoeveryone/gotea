<?php
declare(strict_types=1);

namespace Gotea\Controller\Api;

use Cake\Http\Response;

/**
 * API・所属国コントローラ
 *
 * @property \Gotea\Model\Table\CountriesTable $Countries
 */
class CountriesController extends ApiController
{
    /**
     * 所属国一覧を取得します。
     *
     * @return \Cake\Http\Response 所属国一覧
     */
    public function index(): Response
    {
        $hasTitle = ($this->getRequest()->getQuery('has_title') === '1');

        $countries = $this->Countries->findAllHasCode($hasTitle);

        return $this->renderJson($countries->toArray());
    }
}
