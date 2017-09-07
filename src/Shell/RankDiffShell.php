<?php

namespace App\Shell;

use Cake\Console\Shell;
use Cake\Log\Log;
use Cake\Mailer\MailerAwareTrait;
use Cake\Utility\Hash;
use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;
use Symfony\Component\DomCrawler\Crawler;

/**
 * 段位の差分を抽出するシェル
 */
class RankDiffShell extends Shell
{
    use MailerAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadModel('Players');
    }

    /**
     * シェルのメイン処理
     *
     * @return void
     */
    public function main()
    {
        $koreaDiffs = $this->getDiff('韓国', $this->__korea());
        $taiwanDiffs = $this->getDiff('台湾', $this->__taiwan());
        $results = array_merge($koreaDiffs, $taiwanDiffs);

        // メール送信
        if ($results) {
            $this->getMailer('User')->send('notification',
                ['【自動通知】棋士段位差分抽出', $results]);
        }
    }

    /**
     * 差分データを設定します。
     *
     * @param string $key
     * @param array $results
     * @return void
     */
    private function getDiff($key, $results)
    {
        $values = $this->Players->findRanksCount(null, $key)->filter(function($item, $key) use ($results) {
            $web = $results[$item->rank] ?? null;
            return ($web !== null && $web !== intval($item->count));
        })->map(function($item, $key) use ($results) {
            return "　{$item->name} WEB: {$results[$item->rank]} - DB: {$item->count}";
        });

        if (!$values) {
            return [];
        }

        return [$key => $values];
    }

    /**
     * 韓国棋士の差分チェック
     *
     * @return array 段位と棋士数の一覧
     */
    private function __korea()
    {
        Log::info('韓国棋士の差分を抽出します。');

        $this->results = [];

        $crawler = $this->__getCrawler(env('DIFF_KOREA_URL'));
        $content = $crawler->filter('#content')->first();
        $content->filter('.facetop')->each(function(Crawler $node) {
            $src = $node->filter('img')->attr('src');
            if (preg_match('/list_([1-9])dan/', $src, $matches)) {
                $rank = $matches[1];
                $count = $node->nextAll()->filter('script')->first()->text();
                if (preg_match('/\'([0-9]{1,})+/', $count, $matches)) {
                    $count = $matches[1];
                    $this->results[$rank] = $count;
                }
            }
        });

        return $this->results;
    }

    /**
     * 台湾棋士の差分チェック
     *
     * @return array 段位と棋士数の一覧
     */
    private function __taiwan()
    {
        Log::info('台湾棋士の差分を抽出します。');

        $this->results = [];

        $crawler = $this->__getCrawler(env('DIFF_TAIWAN_URL'));
        $crawler->filter('table[width=685]')->each(function(Crawler $node) {
            $node->filter('tr')->each(function(Crawler $node) {
                $img = $node->filter('img')->first();
                if ($img->count() && count($src = $img->attr('src'))
                    && preg_match('/dan([0-9]{2})/', $src, $matches)) {
                    $rank = intval($matches[1]);
                    $count = $node->filter('td[align=right]')->text();
                    $this->results[$rank] = intval($count);
                }
            });
        });

        return $this->results;
    }

    /**
     * URLからCrawlerオブジェクトを返却
     *
     * @param string $url
     * @return Crawler
     */
    private function __getCrawler($url)
    {
        $client = new Client();
        $client->setClient(new GuzzleClient());
        return $client->request('GET', $url);
    }
}
