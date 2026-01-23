<?php
declare(strict_types=1);

namespace Gotea\Controller\Api;

use Cake\Http\Response;
use Gotea\Collection\Iterator\NewsIterator;
use Gotea\Collection\Iterator\TitlesIterator;
use Gotea\Utility\FileBuilder;

/**
 * API・タイトルコントローラ
 *
 * @property \Gotea\Model\Table\TitlesTable $Titles
 */
class TitlesController extends ApiController
{
    /**
     * タイトルを検索します。
     *
     * @return \Cake\Http\Response|null
     */
    public function index(): ?Response
    {
        // 検索
        $titles = $this->Titles->findTitles($this->getRequest()->getQueryParams());

        return $this->renderJson($titles->all()->map(new TitlesIterator()));
    }

    /**
     * タイトルを登録します。
     *
     * @return \Cake\Http\Response|null
     */
    public function create(): ?Response
    {
        $title = $this->Titles->createEntity(null, $this->getRequest()->getParsedBody());

        if (!$this->Titles->save($title)) {
            return $this->renderError(400, $title->getValidateErrors());
        }

        return $this->renderJson($title->toArray());
    }

    /**
     * タイトルを更新します。
     *
     * @param int $id ID
     * @return \Cake\Http\Response|null
     */
    public function update(int $id): ?Response
    {
        $title = $this->Titles->createEntity($id, $this->getRequest()->getParsedBody());

        if (!$this->Titles->save($title)) {
            return $this->renderError(400, $title->getValidateErrors());
        }

        return $this->renderJson($title->toArray());
    }

    /**
     * Go Newsの出力データを取得します。
     *
     * @return \Cake\Http\Response|null
     */
    public function createNews(): ?Response
    {
        $titles = $this->Titles->findTitles(['search_closed' => true])
            ->all()->map(new NewsIterator());

        // ファイル作成
        $builder = FileBuilder::new();
        if (!$builder->output('news', $titles)) {
            return $this->renderError(500, 'JSON出力失敗');
        }

        return $this->renderJson($titles->toArray());
    }
}
