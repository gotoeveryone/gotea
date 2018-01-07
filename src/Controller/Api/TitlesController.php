<?php

namespace Gotea\Controller\Api;

use Cake\Filesystem\File;
use Cake\Log\Log;
use Gotea\Collection\Iterator\NewsIterator;
use Gotea\Collection\Iterator\TitlesIterator;

/**
 * API・タイトルコントローラ
 */
class TitlesController extends ApiController
{
    /**
     * タイトルを検索します。
     *
     * @return \Cake\Http\Response タイトル情報
     */
    public function index()
    {
        // 検索
        $titles = $this->Titles->findTitles($this->request->getQuery());

        return $this->renderJson($titles->map(new TitlesIterator));
    }

    /**
     * タイトルを登録します。
     *
     * @return \Cake\Http\Response タイトル情報
     */
    public function create()
    {
        $input = $this->request->getParsedBody();
        $title = $this->Titles->createEntity(null, $input);

        if (!$this->Titles->save($title)) {
            return $this->renderError(400, $title->getValidateErrors());
        }

        return $this->renderJson($title->toArray());
    }

    /**
     * タイトルを更新します。
     *
     * @param int $id ID
     * @return \Cake\Http\Response タイトル情報
     */
    public function update(int $id)
    {
        $input = $this->request->getParsedBody();
        $title = $this->Titles->createEntity($id, $input);

        if (!$this->Titles->save($title)) {
            return $this->renderError(400, $title->getValidateErrors());
        }

        return $this->renderJson($title->toArray());
    }

    /**
     * Go Newsの出力データを取得します。
     *
     * @return \Cake\Http\Response Go News出力データ
     */
    public function createNews()
    {
        $titles = $this->Titles->findTitles($this->request->getQuery())
            ->map(new NewsIterator);

        // ファイル作成
        $file = new File(env('JSON_OUTPUT_DIR') . 'news.json');
        Log::info("JSONファイル出力：{$file->path}");

        if (!$file->write(json_encode($titles))) {
            return $this->renderError(500, 'JSON出力失敗');
        }

        return $this->renderJson($titles->toArray());
    }
}
