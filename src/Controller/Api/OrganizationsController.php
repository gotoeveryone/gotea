<?php
declare(strict_types=1);

namespace Gotea\Controller\Api;

/**
 * API・所属組織コントローラ
 *
 * @property \Gotea\Model\Table\OrganizationsTable $Organizations
 */
class OrganizationsController extends ApiController
{
    /**
     * 所属組織一覧を取得します。
     *
     * @return \Cake\Http\Response 所属組織一覧
     */
    public function index()
    {
        $countryId = $this->getRequest()->getQuery('countryId');

        $organizations = $countryId
            ? $this->Organizations->findByCountryId($countryId)
            : $this->Organizations->find();

        return $this->renderJson($organizations);
    }
}
