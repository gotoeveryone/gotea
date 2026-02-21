<?php
declare(strict_types=1);

namespace Gotea\Controller\Api;

use Cake\Http\Response;

/**
 * TableTemplates Controller
 *
 * @property \Gotea\Model\Table\TableTemplatesTable $TableTemplates
 * @method \Cake\Datasource\Paging\PaginatedInterface paginate($object = null, array $settings = [])
 */
class TableTemplatesController extends ApiController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response
     */
    public function index(): Response
    {
        $query = $this->TableTemplates->find()->orderBy('title');
        $tableTemplates = $this->paginate($query);

        return $this->renderJson([
            'total' => $query->count(),
            'items' => collection($tableTemplates->items())->map(function ($item) {
                return $item->toArray();
            })->toList(),
        ]);
    }
}
