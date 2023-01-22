<?php
declare(strict_types=1);

namespace Gotea\Command\SubCommand;

use Cake\Core\Configure;
use Cake\Datasource\ResultSetInterface;
use Cake\Http\Client as HttpClient;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Gotea\Model\Entity\Country;
use Symfony\Component\DomCrawler\Crawler;

class RankDiffJapanSubCommand implements RankDiffSubCommandInterface
{
    use RankDiffTrait;

    /**
     * @var \Gotea\Model\Entity\Country
     */
    private Country $country;

    /**
     * Constructor
     *
     * @param \Gotea\Model\Entity\Country $country 所属国
     */
    public function __construct(Country $country)
    {
        $this->country = $country;
    }

    /**
     * @inheritDoc
     */
    public function getPlayers(HttpClient $client, array $ranks): array
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

        /** @var \Gotea\Model\Table\PlayersTable $players */
        $players = TableRegistry::getTableLocator()->get('Players');

        // 段位が設定されていない場合は DB から段位を割り当てる
        foreach ($results as $item) {
            if ($item['rank'] === null) {
                foreach ($item['players'] as $name) {
                    $player = $players->findRankByNamesAndCountries(
                        [$name, str_replace('　', '', $name)],
                        $this->country->id
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
     * @inheritDoc
     */
    public function getRankSummary(): ResultSetInterface
    {
        /** @var \Gotea\Model\Table\PlayersTable $players */
        $players = TableRegistry::getTableLocator()->get('Players');

        return $players->findRanksCount($this->country->id, null)->all();
    }

    /**
     * 日本棋院の棋士一覧を取得
     *
     * @param array $ranks 段位一覧
     * @return array 日本棋院の棋士一覧
     */
    private function getPlayersFromNihonKiin(array $ranks): array
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
    private function getPlayersFromKansaiKiin(array $ranks): array
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
