<?php
declare(strict_types=1);

namespace Gotea\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Log\Log;
use Cake\ORM\Query;
use Cake\Utility\Inflector;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

/**
 * 昇段履歴を追加するコマンド
 *
 * @property \Gotea\Model\Table\PlayersTable $Players
 * @property \Gotea\Model\Table\PlayerRanksTable $PlayerRanks
 * @property \Gotea\Model\Table\CountriesTable $Countries
 * @property \Gotea\Model\Table\RanksTable $Ranks
 *
 * phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedMethod
 */
class PlayerRankCommand extends Command
{
    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->Players = $this->fetchTable('Players');
        $this->PlayerRanks = $this->fetchTable('PlayerRanks');
        $this->Countries = $this->fetchTable('Countries');
        $this->Ranks = $this->fetchTable('Ranks');
    }

    /**
     * @inheritDoc
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser->addArgument('code', [
            'help' => 'country code',
            'required' => true,
            'choices' => ['kr'],
        ]);

        return $parser;
    }

    /**
     * メイン処理
     *
     * @param \Cake\Console\Arguments $args 引数
     * @param \Cake\Console\ConsoleIo $io 入出力
     * @return int Success or error code.
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        Log::info('昇段情報の取り込みを開始します。');
        $code = $args->getArgumentAt(0);

        // 段位一覧を抽出
        $ranks = $this->Ranks->findProfessional();
        $results = [];

        // 対象国を決定
        $country = $this->Countries->findByCode($code)->first();
        $target = Inflector::humanize($country->name_english);

        // 取得
        $getMethod = 'getPlayerFrom' . $target;
        if (!method_exists($this, $getMethod)) {
            $io->err('Method not implemented.');

            return self::CODE_ERROR;
        }
        $results = $this->$getMethod();

        // 保存
        $saveMethod = 'savePlayerRanksTo' . $target;
        if (!method_exists($this, $saveMethod)) {
            $io->err('Method not implemented.');

            return self::CODE_ERROR;
        }

        $allCount = 0;
        foreach ($results as $result) {
            $res = $this->$saveMethod($io, $country->id, $result, $ranks);
            if ($res === false) {
                $io->out('失敗');
            }

            $allCount = $allCount + count($res);
        }

        $count = count($results);
        Log::info("${count}人の昇段情報（全${allCount}件）を登録しました。");
        Log::info('昇段情報の取り込みを終了します。');

        return self::CODE_SUCCESS;
    }

    /**
     * 韓国棋士の段位と棋士数を取得
     *
     * @return array 段位と棋士の一覧
     */
    private function getPlayerFromKorea(): array
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
     * @param \Cake\Console\ConsoleIo $io 入出力
     * @param int $countryId 所属国ID
     * @param string $url URL
     * @param \Cake\ORM\Query $ranks 段位一覧
     * @return array|false 保存結果
     */
    private function savePlayerRanksToKorea(ConsoleIo $io, int $countryId, string $url, Query $ranks): array|false
    {
        $crawler = $this->getCrawler("http://www.baduk.or.kr/info/${url}");
        $name = $crawler->filter('.faceinfo .r strong')->first()->text();

        // 段位と昇段日を抜き出す
        $text = $crawler->filter('.profile')->first()->text();
        if (!preg_match_all('/([1-9])단  \/ ([0-9]{4}\.[0-9]{1,2}\.[0-9]{1,2}) /', $text, $matches)) {
            return [];
        }

        $player = $this->Players->find()->where([
            'country_id' => $countryId,
            'name_other' => $name,
        ])->first();
        if (!$player) {
            $io->err('棋士名が見つからない！ ' . $name);

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
    private function getCrawler(string $url): Crawler
    {
        $client = new Client();

        return $client->request('GET', $url);
    }
}
