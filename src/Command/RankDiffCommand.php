<?php
declare(strict_types=1);

namespace Gotea\Command;

use Cake\Collection\Collection;
use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Core\Configure;
use Cake\Http\Client as HttpClient;
use Cake\Http\Exception\HttpException;
use Cake\I18n\FrozenDate;
use Cake\Log\Log;
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
 * @property \Gotea\Model\Table\OrganizationsTable $Organizations
 *
 * phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedMethod
 */
class RankDiffCommand extends Command
{
    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadModel('Players');
        $this->loadModel('Countries');
        $this->loadModel('Ranks');
        $this->loadModel('Organizations');
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
        $today = FrozenDate::now()->format('Ymd');
        $url = Configure::read('App.slack.notifyUrl');
        $client = new HttpClient();
        try {
            $countries = $this->Countries->find()->where(['name in' => ['日本', '韓国', '台湾']]);
            $ranks = $this->Ranks->findProfessional()->combine('name', 'rank_numeric')->toArray();
            $results = $countries->combine('name', function ($item) use ($ranks) {
                Log::info("{$item->name}棋士の差分を抽出します。");
                $method = 'getPlayersFrom' . Inflector::humanize($item->name_english);
                $diffs = $this->$method($item, $ranks);
                if (!count($diffs)) {
                    return [];
                }

                return $this->getDiff($item, $diffs);
            });

            if ($url && !$results->unfold()->isEmpty()) {
                $client->post($url, json_encode([
                    'username' => 'gotea',
                    'link_names' => true,
                    'text' => $this->getNotifyContent($results),
                ]), [
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                ]);
            }

            return self::CODE_SUCCESS;
        } catch (Throwable $ex) {
            Log::error($ex->getMessage());

            if ($url) {
                $client->post($url, json_encode([
                    'username' => 'gotea',
                    'link_names' => true,
                    'text' => '段位差分抽出時にエラーが発生しました。',
                ]), [
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                ]);
            }

            throw $ex;
        }
    }

    /**
     * 差分データを設定します。
     *
     * @param \Gotea\Model\Entity\Country $country 所属国
     * @param array $results 抽出結果
     * @return array
     */
    private function getDiff($country, $results)
    {
        // 台湾の場合は台湾棋院のみを対象とする
        $organization = $country->code === 'tw' ? $this->Organizations->findByName('台湾棋院')->first() : null;

        return $this->Players->findRanksCount($country->id, $organization ? $organization->id : null)
            ->map(function ($item) use ($results) {
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
     * @param array $ranks 段位一覧
     * @return array 段位と棋士の一覧
     */
    private function getPlayersFromJapan(Country $country, array $ranks)
    {
        // 日本棋院・関西棋院それぞれから棋士一覧を取得
        $nihonkiin = $this->getPlayersFromNihonKiin($ranks);
        $kansaikiin = $this->getPlayersFromKansaiKiin($ranks);
        // 対象の段位を取得
        // タイトル者がどちらかにしかいないケースを考慮し、rankText の値をマージしたうえで重複を排除する
        $rankTexts = array_unique(
            Hash::merge(
                Hash::extract($nihonkiin, '{n}.rankText'),
                Hash::extract($kansaikiin, '{n}.rankText')
            )
        );

        $results = Hash::map($rankTexts, '{n}', function ($rankText) use ($ranks, $nihonkiin, $kansaikiin) {
            return [
                'rankText' => $rankText,
                'rank' => Hash::get($ranks, $rankText),
                'players' => Hash::merge(
                    collection($nihonkiin)
                        ->filter(function ($item) use ($rankText) {
                            return Hash::get($item, 'rankText') === $rankText;
                        })
                        ->extract('players')
                        ->unfold()
                        ->toList(),
                    collection($kansaikiin)
                        ->filter(function ($item) use ($rankText) {
                            return Hash::get($item, 'rankText') === $rankText;
                        })
                        ->extract('players')
                        ->unfold()
                        ->toList()
                ),
            ];
        });

        // 段位が設定されていない場合は DB から段位を割り当てる
        foreach ($results as $item) {
            if ($item['rank'] === null) {
                foreach ($item['players'] as $name) {
                    $player = $this->Players->findRankByNamesAndCountries(
                        [$name, str_replace('　', '', $name)],
                        $country->id
                    );
                    foreach ($results as $idx => $data) {
                        if ($data['rank'] === $player->rank->rank_numeric) {
                            $results[$idx]['players'][] = $player->name;
                        }
                    }
                }
            }
        }

        return collection($results)->filter(function ($item) {
            return $item['rank'];
        })->combine('rank', 'players')->toArray();
    }

    /**
     * 韓国棋士の段位と棋士数を取得
     *
     * @param \Gotea\Model\Entity\Country $country 所属国
     * @param array $ranks 段位一覧
     * @return array 段位と棋士の一覧
     */
    private function getPlayersFromKorea(Country $country, array $ranks)
    {
        $crawler = $this->getCrawler(Configure::read('App.diffUrl.korea'));

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
     * @param array $ranks 段位一覧
     * @return array 段位と棋士の一覧
     */
    private function getPlayersFromTaiwan(Country $country, array $ranks)
    {
        $results = [];
        $rank = null;
        $crawler = $this->getCrawler(Configure::read('App.diffUrl.taiwan'));
        $crawler->filter('.post-body.entry-content div:first-child > div')
            ->each(function (Crawler $node) use (&$results, &$rank, $ranks) {
                // テキストが設定されている場合のみ処理する
                $text = trim($node->text());
                if ($text) {
                    $matches = [];
                    if (preg_match('/(.*段).*\(\d+\)/', $text, $matches)) {
                        $rank = Hash::get($ranks, $matches[1]);
                    } else {
                        $players = Hash::filter($node->filter('a')->each(function ($node) {
                            return trim(preg_replace("/\s+/u", '', $node->text()));
                        }), function ($name) {
                            return !empty($name);
                        });
                        if ($players) {
                            $results[$rank] = Hash::merge(Hash::get($results, $rank, []), $players);
                        }
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
     * @throws \Cake\Http\Exception\HttpException
     */
    private function getCrawler($url)
    {
        $client = new Client();
        $client->setClient(new GuzzleClient());

        $crawler = $client->request('GET', $url);
        if ($client->getInternalResponse()->getStatusCode() >= 400) {
            throw new HttpException(
                'クロール先のページが意図しないレスポンスを返しました。',
                $client->getInternalResponse()->getStatusCode()
            );
        }

        return $crawler;
    }

    /**
     * 日本棋院の棋士一覧を取得
     *
     * @param array $ranks 段位一覧
     * @return array 日本棋院の棋士一覧
     */
    private function getPlayersFromNihonKiin(array $ranks)
    {
        $crawler = $this->getCrawler(Configure::read('App.diffUrl.japan'));

        return $crawler->filter('#content h2')->each(function (Crawler $node) use ($ranks) {
            $rankText = $node->text();
            $rank = Hash::get($ranks, $rankText);
            $players = $node->nextAll()->filter('.ul_players')->first()
                ->filter('li')->each(function (Crawler $cell) {
                    return $cell->text();
                });

            return compact('rankText', 'rank', 'players');
        });
    }

    /**
     * 関西棋院の棋士一覧を取得
     *
     * @param array $ranks 段位一覧
     * @return array 関西棋院の棋士一覧
     */
    private function getPlayersFromKansaiKiin(array $ranks)
    {
        $crawler = $this->getCrawler(Configure::read('App.diffUrl.kansai'));

        return collection($crawler->filter('.prokisi_table table')->each(function (Crawler $table) use ($ranks) {
            $rankText = $table->filter('thead th')->first()->text();
            $rank = Hash::get($ranks, $rankText);
            $players = collection($table->filter('tbody td')->each(function (Crawler $cell) {
                return $cell->text();
            }))->filter(function ($value) {
                return mb_strlen($value) > 0;
            })->toArray();

            return compact('rankText', 'rank', 'players');
        }))->filter(function ($item) {
            // 退役者や物故者は除く
            return $item['rank'] || $item['rankText'] === 'タイトル者';
        })->toArray();
    }

    /**
     * 通知コンテンツを取得する
     *
     * @param \Cake\Collection\Collection $messages 本文
     * @return string 通知コンテンツ
     */
    private function getNotifyContent(Collection $messages): string
    {
        return implode("\n", [
            '段位差分がありました。',
            '```',
            implode("\n", $messages->filter(function ($values, $key) {
                return count($values) > 0;
            })->map(function ($values, $key) {
                return implode("\n", array_merge(["【${key}】"], array_map(function ($value) {
                    return $value;
                }, array_values($values))));
            })->toArray()),
            '```',
        ]);
    }
}
