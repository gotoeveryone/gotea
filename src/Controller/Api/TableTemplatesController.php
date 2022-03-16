<?php
declare(strict_types=1);

namespace Gotea\Controller\Api;

/**
 * TableTemplates Controller
 *
 * @property \Gotea\Model\Table\TableTemplatesTable $TableTemplates
 * @method \Gotea\Model\Entity\TableTemplate[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TableTemplatesController extends ApiController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response
     */
    public function index()
    {
        $query = $this->TableTemplates->find()->orderAsc('title');
        $tableTemplates = $this->paginate($query);

        return $this->renderJson([
            'total' => $query->count(),
            'items' => $tableTemplates->map(function ($item) {
                return $item->toArray();
            }),
        ]);
    }
}
