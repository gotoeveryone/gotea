<?php
declare(strict_types=1);

namespace Gotea\Controller\Api;

use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\Http\Response;
use Cake\Log\Log;
use Gotea\Collection\Iterator\NewsIterator;
use Gotea\Collection\Iterator\TitlesIterator;

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

        return $this->renderJson($titles->map(new TitlesIterator()));
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
     * @param string $id ID
     * @return \Cake\Http\Response|null
     */
    public function update(string $id): ?Response
    {
        $title = $this->Titles->createEntity((int)$id, $this->getRequest()->getParsedBody());

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
            ->map(new NewsIterator());

        // ファイル作成
        $file = new File(Configure::read('App.jsonDir') . 'news.json');
        Log::info("JSONファイル出力：{$file->path}");

        if (!$file->write(json_encode($titles))) {
            return $this->renderError(500, 'JSON出力失敗');
        }

        return $this->renderJson($titles->toArray());
    }
}
