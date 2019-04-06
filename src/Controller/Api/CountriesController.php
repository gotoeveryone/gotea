<?php

namespace Gotea\Controller\Api;

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
    public function index()
    {
        $hasTitle = ($this->getRequest()->getQuery('has_title') === '1');

        $countries = $this->Countries->findAllHasCode($hasTitle);

        return $this->renderJson($countries);
    }
}
