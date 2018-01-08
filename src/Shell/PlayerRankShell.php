<?php
namespace Gotea\Shell;

use Cake\Console\Shell;
use Cake\ORM\Query;
use Cake\Utility\Inflector;
use Gotea\Model\Entity\PlayerRank;
use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;
use Symfony\Component\DomCrawler\Crawler;

/**
 * AddPlayerRank shell command.
 */
class PlayerRankShell extends Shell
{

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadModel('Players');
        $this->loadModel('PlayerRanks');
        $this->loadModel('Countries');
        $this->loadModel('Ranks');
    }

    /**
     * Manage the available sub-commands along with their arguments and help
     *
     * @see http://book.cakephp.org/3.0/en/console-and-shells.html#configuring-options-and-generating-help
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        $parser->addArgument('code', [
            'help' => 'country code',
            'required' => true,
            'choices' => ['kr'],
        ]);

        return $parser;
    }

    /**
     * main() method.
     *
     * @return bool|int|null Success or error code.
     */
    public function main()
    {
        $this->out($this->OptionParser->help());

        return true;
    }

    /**
     * main() method.
     *
     * @param string|null $code コード
     * @return bool|int|null Success or error code.
     */
    public function add(string $code = null)
    {
        if (empty($this->args['code'])) {
            $this->out($this->OptionParser->help());

            return false;
        }

        // 段位一覧を抽出
        $ranks = $this->Ranks->findProfessional();
        $results = [];

        $country = $this->Countries->findByCode($this->args['code'])->first();
        $method = 'getPlayerFrom' . Inflector::humanize($item->name_english);
        if (method_exists($this, $method)) {
            $results = $this->$method();
        }

        $saveMethod = 'savePlayerRanksTo' . Inflector::humanize($item->name_english);
        if (method_exists($this, $saveMethod)) {
            $allCount = 0;

            foreach ($results as $result) {
                $res = $this->$saveMethod($country->id, $result, $ranks);
                if ($res === false) {
                    $this->out('失敗');
                }

                $allCount = $allCount + count($res);
            }

            $count = count($results);
            $this->out("${count}人の昇段情報（全${allCount}件）を登録しました。");
        }

        return true;
    }

    /**
     * 韓国棋士の段位と棋士数を取得
     *
     * @return array 段位と棋士の一覧
     */
    private function getPlayerFromKorea()
    {
        $crawler = $this->getCrawler(env('DIFF_KOREA_URL'));

        return collection($crawler->filter('#content .facetop')->each(function (Crawler $node) {
            return $node->nextAll()->filter('table')->first()->filter('td')->each(function (Crawler $node) {
                $links = $node->filter('a');
                if (!$links->count()) {
                    return '';
                }

                return $links->first()->attr('href');
            });
        }))
        ->unfold()
        ->filter(function ($item) {
            return strlen($item) > 0;
        })
        ->toList();
    }

    /**
     * 棋士の昇段情報を取得します。
     *
     * @param int $countryId 所属国ID
     * @param string $url URL
     * @param \Cake\ORM\Query $ranks 段位一覧
     * @return array|false 保存結果
     */
    private function savePlayerRanks(int $countryId, string $url, Query $ranks)
    {
        $crawler = $this->getCrawler("http://www.baduk.or.kr/info/${url}");
        $name = $crawler->filter('.faceinfo .r strong')->first()->text();

        // 1단  / 1958.10.
        $text = $crawler->filter('.profile')->first()->text();
        if (!preg_match_all('/([1-9])단  \/ ([0-9]{4}\.[0-9]{1,2}\.[0-9]{1,2}) /', $text, $matches)) {
            return [];
        }

        $player = $this->Players->find()->where([
            'country_id' => $countryId,
            'name_other' => $name,
        ])->first();
        if (!$player) {
            $this->err('棋士名が見つからない！ ' . $name);

            return false;
        }

        $playerRanks = $this->PlayerRanks->findByPlayerId($player->id);

        $saveRanks = [];
        foreach ($matches[1] as $idx => $match) {
            $rank = $ranks->filter(function ($item) use ($match) {
                return $item->rank_numeric === (int)$match;
            })->first();

            $playerRank = $playerRanks->filter(function ($item) use ($rank) {
                return $item->rank_id === $rank->id;
            })->first();

            // すでに昇段情報があれば何もしない
            if ($playerRank) {
                continue;
            }

            $saveRanks[] = $this->PlayerRanks->newEntity([
                'player_id' => $player->id,
                'rank_id' => $rank->id,
                'promoted' => $matches[2][$idx],
            ]);
        }

        if (!$saveRanks) {
            return [];
        }

        return $this->PlayerRanks->saveMany($saveRanks);
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

        return $client->request('GET', $url);
    }
}
