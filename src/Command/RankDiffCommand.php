<?php

namespace Gotea\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Core\Configure;
use Cake\Http\Exception\HttpException;
use Cake\I18n\FrozenDate;
use Cake\Log\Log;
use Cake\Mailer\MailerAwareTrait;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Gotea\Model\Entity\Country;
use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;
use Symfony\Component\DomCrawler\Crawler;
use Throwable;

/**
 * 段位の差分を抽出するコマンド
 *
 * @property \Gotea\Model\Table\PlayersTable $Players
 * @property \Gotea\Model\Table\CountriesTable $Countries
 * @property \Gotea\Model\Table\RanksTable $Ranks
 */
class RankDiffCommand extends Command
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
     * メイン処理
     *
     * @param \Cake\Console\Arguments $args 引数
     * @param \Cake\Console\ConsoleIo $io 入出力
     * @return int Success or error code.
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $mailer = $this->getMailer('User');
        $today = FrozenDate::now()->format('Ymd');
        try {
            $countries = $this->Countries->find()->where(['name in' => ['日本', '韓国', '台湾']]);
            $results = $countries->combine('name', function ($item) {
                Log::info("{$item->name}棋士の差分を抽出します。");
                $method = 'getPlayerFrom' . Inflector::humanize($item->name_english);
                $diffs = $this->$method($item);
                if (!count($diffs)) {
                    return [];
                }

                return $this->getDiff($item->id, $diffs);
            });

            if (!Configure::read('debug')) {
                // メール送信
                if (!$results->unfold()->isEmpty()) {
                    $subject = "【自動通知】${today}_棋士段位差分抽出";
                    $mailer->send('notification', [$subject, $results]);
                }
            } else {
                $results->filter(function ($item) {
                    return count($item) > 0;
                })->each(function ($item, $key) {
                    Log::info($key);
                    Log::info($item);
                });
            }

            return self::CODE_SUCCESS;
        } catch (Throwable $ex) {
            Log::error($ex);

            if (!Configure::read('debug')) {
                $subject = "【自動通知】${today}_棋士段位差分抽出_異常終了";
                $mailer->send('error', [$subject, $ex]);
            }

            return self::CODE_ERROR;
        }
    }

    /**
     * 差分データを設定します。
     *
     * @param int $countryId 所属国ID
     * @param array $results 抽出結果
     * @return array
     */
    private function getDiff($countryId, $results)
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
     * @param \Gotea\Model\Entity\Country $country 所属国
     * @return array 段位と棋士の一覧
     */
    private function getPlayerFromJapan(Country $country)
    {
        $ranks = $this->Ranks->findProfessional()->combine('name', 'rank_numeric')->toArray();
        $results = [];

        // 日本棋院
        $crawler = $this->getCrawler(env('DIFF_JAPAN_URL'));
        $crawler->filter('#content h2')->each(function (Crawler $node) use (&$results, $country, $ranks) {
            if ($node->text() === 'タイトル者') {
                $node->nextAll()->filter('.ul_players')->first()
                    ->filter('li')->each(function (Crawler $node) use (&$results, $country) {
                        $name = str_replace('　', '', $node->text());
                        $player = $this->Players->findRankByNamesAndCountries([$name, $node->text()], $country->id);
                        $results[$player->rank->rank_numeric][] = $name;
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
        $crawler = $this->getCrawler(env('DIFF_KANSAI_URL'));
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
     * @param \Gotea\Model\Entity\Country $country 所属国
     * @return array 段位と棋士の一覧
     */
    private function getPlayerFromKorea(Country $country)
    {
        $crawler = $this->getCrawler(env('DIFF_KOREA_URL'));

        return collection($crawler->filter('.playerArea .lv_list')
            ->each(function (Crawler $row) {
                $rank = $row->filter('.lv dt span')->first()->text();
                $players = collection($row->filter('.players .player')
                    ->each(function (Crawler $node) {
                        return $node->text();
                    }))->toArray();

                return compact('rank', 'players');
            }))->combine('rank', 'players')->toArray();
    }

    /**
     * 台湾棋士の段位と棋士数を取得
     *
     * @param \Gotea\Model\Entity\Country $country 所属国
     * @return array 段位と棋士の一覧
     */
    private function getPlayerFromTaiwan(Country $country)
    {
        $results = [];
        $crawler = $this->getCrawler(env('DIFF_TAIWAN_URL'));
        $crawler->filter('table[width=685] tr')->each(function (Crawler $node) use (&$results) {
            $img = $node->filter('img')->first();
            if ($img->count() > 0) {
                if (preg_match('/dan([0-9]{2})/', $img->attr('src'), $matches)) {
                    $rank = intval($matches[1]);
                    $playerNodes = $node->nextAll()->filter('tr')->first()->filter('td[colspan=2] div');
                    $results[$rank] = $playerNodes->each(function ($node) {
                        return $node->text();
                    });
                }
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
    private function getCrawler($url)
    {
        $client = new Client();
        $client->setClient(new GuzzleClient());

        $crawler = $client->request('GET', $url);
        if ($client->getInternalResponse()->getStatus() >= 400) {
            throw new HttpException(
                'クロール先のページが意図しないレスポンスを返しました。',
                $client->getInternalResponse()->getStatus()
            );
        }

        return $crawler;
    }
}
