<?php

namespace Gotea\Shell;

use Cake\Console\Shell;
use Cake\I18n\FrozenDate;
use Cake\Log\Log;
use Cake\Mailer\MailerAwareTrait;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Exception;
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
        $this->loadModel('Countries');
        $this->loadModel('Ranks');
    }

    /**
     * シェルのメイン処理
     *
     * @return void
     */
    public function main()
    {
        try {
            $countries = $this->Countries->find()->where(['name in' => ['日本', '韓国', '台湾']]);
            $results = $countries->combine('name', function ($item) {
                Log::info("{$item->name}棋士の差分を抽出します。");
                $method = '__getPlayerFrom' . Inflector::humanize($item->name_english);

                return $this->__getDiff($item->id, $this->$method());
            });

            // メール送信
            if (!$results->unfold()->isEmpty()) {
                $today = FrozenDate::now()->format('Ymd');
                $subject = "【自動通知】${today}_棋士段位差分抽出";
                $this->getMailer('User')->send('notification', [$subject, $results]);
            }
        } catch (Exception $ex) {
            Log::error($ex);
            $today = FrozenDate::now()->format('Ymd');
            $subject = "【自動通知】${today}_棋士段位差分抽出_異常終了";
            $this->getMailer('User')->send('error', [$subject, $ex]);
        }
    }

    /**
     * 差分データを設定します。
     *
     * @param int $countryId 所属国ID
     * @param array $results 抽出結果
     * @return array
     */
    private function __getDiff($countryId, $results)
    {
        return $this->Players->findRanksCount($countryId)->map(function ($item) use ($results) {
            $item->web_count = count($results[$item->rank]) ?? 0;

            return $item;
        })->filter(function ($item) {
            return $item->web_count !== $item->count;
        })->map(function ($item) {
            return "　{$item->name} WEB: {$item->web_count} - DB: {$item->count}";
        })->toArray();
    }

    /**
     * 日本棋士の段位と棋士数を取得
     *
     * @return array 段位と棋士の一覧
     */
    private function __getPlayerFromJapan()
    {
        $ranks = $this->Ranks->findProfessional()->combine('name', 'rank_numeric')->toArray();
        $results = [];

        // 日本棋院
        $crawler = $this->__getCrawler(env('DIFF_JAPAN_URL'));
        $crawler->filter('#content h2')->each(function (Crawler $node) use (&$results, $ranks) {
            if ($node->text() === 'タイトル者') {
                $node->nextAll()->filter('.ul_players')->first()
                    ->filter('li')->each(function (Crawler $node) use (&$results) {
                        $name = str_replace('　', '', $node->text());
                        $player = $this->Players->findByName($name)
                            ->contain('Ranks')->select(['rank_num' => 'Ranks.rank_numeric'])->first();
                        $results[$player->rank_num][] = $name;
                    });
            }
            if (preg_match('/(.*)段/', $node->text())) {
                $players = $node->nextAll()->filter('.ul_players')->first()
                    ->filter('li')->each(function (Crawler $node) {
                        return str_replace('　', '', $node->text());
                    });
                $rank = Hash::get($ranks, $node->text());
                if (Hash::check($results, $rank)) {
                    $players = Hash::merge(Hash::get($results, $rank), $players);
                }
                $results[$rank] = $players;
            }
        });

        // 関西棋院
        $crawler = $this->__getCrawler(env('DIFF_KANSAI_URL'));
        $rank = null;
        $crawler->filter('.free table')->first()->filter('tr')
            ->each(function (Crawler $row) use (&$results, &$rank, $ranks) {
                $cell = $row->children();
                if ($cell->count() === 1) {
                    // 段位
                    $rank = $ranks[$cell->text()] ?? null;
                } elseif ($rank) {
                    // 棋士数
                    $cell->each(function (Crawler $node) use (&$results, &$rank) {
                        if ($node->text() !== '') {
                            $results[$rank][] = $node->text();
                        }
                    });
                }
            });

        return $results;
    }

    /**
     * 韓国棋士の段位と棋士数を取得
     *
     * @return array 段位と棋士の一覧
     */
    private function __getPlayerFromKorea()
    {
        $results = [];
        $crawler = $this->__getCrawler(env('DIFF_KOREA_URL'));
        $crawler->filter('#content .facetop')->each(function (Crawler $node) use (&$results) {
            $src = $node->filter('img')->attr('src');
            if (preg_match('/list_([1-9])dan/', $src, $matches)) {
                $rank = $matches[1];
                $playerNodes = $node->nextAll()->filter('table')->first()->filter('td');
                $players = $playerNodes->each(function ($node) {
                    return $node->text();
                });

                $results[$rank] = collection($players)->filter(function ($item, $key) {
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
        $results = [];
        $crawler = $this->__getCrawler(env('DIFF_TAIWAN_URL'));
        $crawler->filter('table[width=685] tr')->each(function (Crawler $node) use (&$results) {
            $img = $node->filter('img')->first();
            if ($img->count() && count($src = $img->attr('src'))
                && preg_match('/dan([0-9]{2})/', $src, $matches)) {
                $rank = intval($matches[1]);
                $playerNodes = $node->nextAll()->filter('tr')->first()->filter('td[colspan=2] div');
                $results[$rank] = $playerNodes->each(function ($node) {
                    return $node->text();
                });
            }
        });

        return $results;
    }

    /**
     * URLからCrawlerオブジェクトを返却
     *
     * @param string $url URL
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    private function __getCrawler($url)
    {
        $client = new Client();
        $client->setClient(new GuzzleClient());

        return $client->request('GET', $url);
    }
}
