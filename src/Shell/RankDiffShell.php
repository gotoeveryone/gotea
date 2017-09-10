<?php

namespace App\Shell;

use Cake\Console\Shell;
use Cake\Log\Log;
use Cake\Mailer\MailerAwareTrait;
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
        $koreaDiffs = $this->__getDiff('韓国', $this->__getPlayerFromKorea());
        $taiwanDiffs = $this->__getDiff('台湾', $this->__getPlayerFromTaiwan());
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
    private function __getDiff($key, $results)
    {
        $values = $this->Players->findRanksCount(null, $key)->filter(function($item, $key) use ($results) {
            $web = count($results[$item->rank]) ?? 0;
            return $web !== $item->count;
        })->map(function($item, $key) use ($results) {
            $web = count($results[$item->rank]) ?? 0;
            return "　{$item->name} WEB: {$web} - DB: {$item->count}";
        });

        if (!$values) {
            return [];
        }

        return [$key => $values];
    }

    /**
     * 韓国棋士の段位と棋士数を取得
     *
     * @return array 段位と棋士の一覧
     */
    private function __getPlayerFromKorea()
    {
        Log::info('韓国棋士の差分を抽出します。');

        $results = [];
        $crawler = $this->__getCrawler(env('DIFF_KOREA_URL'));
        $crawler->filter('#content .facetop')->each(function(Crawler $node) use (&$results) {
            $src = $node->filter('img')->attr('src');
            if (preg_match('/list_([1-9])dan/', $src, $matches)) {
                $rank = $matches[1];
                $playerNodes = $node->nextAll()->filter('table')->first()->filter('td');
                $players = $playerNodes->each(function($node) {
                    return $node->text();
                });

                $results[$rank] = collection($players)->filter(function($item, $key) {
                    $text = $item;
                    if (preg_match('/(\s)/u', $text)) {
                        $text = preg_replace('/(\s)/u', '', $text);
                    }
                    return $text !== '';
                })->toArray();
            }
        });

        return $results;
    }

    /**
     * 台湾棋士の段位と棋士数を取得
     *
     * @return array 段位と棋士の一覧
     */
    private function __getPlayerFromTaiwan()
    {
        Log::info('台湾棋士の差分を抽出します。');

        $results = [];
        $crawler = $this->__getCrawler(env('DIFF_TAIWAN_URL'));
        $crawler->filter('table[width=685] tr')->each(function(Crawler $node) use (&$results) {
            $img = $node->filter('img')->first();
            if ($img->count() && count($src = $img->attr('src'))
                && preg_match('/dan([0-9]{2})/', $src, $matches)) {
                $rank = intval($matches[1]);
                $playerNodes = $node->nextAll()->filter('tr')->first()->filter('td[colspan=2] div');
                $results[$rank] = $playerNodes->each(function($node) {
                    return $node->text();
                });
            }
        });

        return $results;
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
