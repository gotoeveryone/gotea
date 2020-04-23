<?php
declare(strict_types=1);

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
 * @property \Gotea\Model\Table\OrganizationsTable $Organizations
 *
 * phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedMethod
 */
class RankDiffCommand extends Command
{
    use MailerAwareTrait;

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
        $mailer = $this->getMailer('User');
        $today = FrozenDate::now()->format('Ymd');
        try {
            $countries = $this->Countries->find()->where(['name in' => ['日本', '韓国', '台湾']]);
            $results = $countries->combine('name', function ($item) {
                Log::info("{$item->name}棋士の差分を抽出します。");
                $method = 'getPlayersFrom' . Inflector::humanize($item->name_english);
                $diffs = $this->$method($item);
                if (!count($diffs)) {
                    return [];
                }

                return $this->getDiff($item, $diffs);
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
     * @return array 段位と棋士の一覧
     */
    private function getPlayersFromJapan(Country $country)
    {
        $ranks = $this->Ranks->findProfessional()->combine('name', 'rank_numeric')->toArray();

        // 日本棋院・関西棋院それぞれから棋士一覧を取得
        $results = Hash::merge($this->getPlayersFromNihonKiin($ranks), $this->getPlayersFromKansaiKiin($ranks));

        // タイトル者は DB から段位を割り当てる
        foreach ($results as $item) {
            if ($item['rankText'] === 'タイトル者') {
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
     * @return array 段位と棋士の一覧
     */
    private function getPlayersFromKorea(Country $country)
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
     * @return array 段位と棋士の一覧
     */
    private function getPlayersFromTaiwan(Country $country)
    {
        $results = [];
        $crawler = $this->getCrawler(Configure::read('App.diffUrl.taiwan'));
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
     * @throws \Cake\Http\Exception\HttpException
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
}
