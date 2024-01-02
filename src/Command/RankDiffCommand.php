<?php
declare(strict_types=1);

namespace Gotea\Command;

use Cake\Collection\Collection;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Core\Configure;
use Cake\Http\Client as HttpClient;
use Cake\Log\Log;
use Gotea\Command\SubCommand\RankDiffJapanSubCommand;
use Gotea\Command\SubCommand\RankDiffKoreaSubCommand;
use Gotea\Command\SubCommand\RankDiffSubCommandInterface;
use Gotea\Command\SubCommand\RankDiffTaiwanSubCommand;
use Gotea\Model\Entity\Country;
use LogicException;
use Throwable;

/**
 * 段位の差分を抽出するコマンド
 *
 * @property \Gotea\Model\Table\CountriesTable $Countries
 * @property \Gotea\Model\Table\RanksTable $Ranks
 */
class RankDiffCommand extends Command
{
    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->Countries = $this->fetchTable('Countries');
        $this->Ranks = $this->fetchTable('Ranks');
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $url = Configure::read('App.slack.notifyUrl');
        $client = new HttpClient();
        try {
            $countries = $this->Countries->find()->where(['name_english in' => ['Japan', 'Korea', 'Taiwan']]);
            $ranks = $this->Ranks->findProfessional()
                ->all()
                ->combine('name', 'rank_numeric')
                ->toArray();
            $results = $countries->all()->combine('name', function ($country) use ($client, $ranks) {
                try {
                    Log::info("{$country->name}棋士の差分を抽出します。");
                    $subCommand = $this->getSubCommand($country);
                    $players = $subCommand->getPlayers($client, $ranks);
                    if (!count($players)) {
                        return [];
                    }

                    return $this->getDiff($subCommand, $players);
                } catch (Throwable $ex) {
                    return ['　エラーにより処理できませんでした。'];
                }
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
     * サブコマンドを取得する
     *
     * @param \Gotea\Model\Entity\Country $country 所属国
     * @return \Gotea\Command\SubCommand\RankDiffSubCommandInterface
     * @throws \LogicException
     */
    private function getSubCommand(Country $country): RankDiffSubCommandInterface
    {
        switch ($country->name_english) {
            case 'Japan':
                return new RankDiffJapanSubCommand($country);
            case 'Korea':
                return new RankDiffKoreaSubCommand($country);
            case 'Taiwan':
                return new RankDiffTaiwanSubCommand($country);
            default:
                throw new LogicException("not implemented subcommand [{$country->name_english}]");
        }
    }

    /**
     * 差分データを設定します。
     *
     * @param \Gotea\Command\SubCommand\RankDiffSubCommandInterface $subCommand サブコマンド
     * @param array $results 抽出結果
     * @return array
     */
    private function getDiff(RankDiffSubCommandInterface $subCommand, array $results): array
    {
        return $subCommand->getRankSummary()
            ->map(function ($item) use ($results) {
                $item->web_count = count($results[$item->rank_numeric]) ?? 0;

                return $item;
            })->filter(function ($item) {
                return $item->web_count !== $item->count;
            })->map(function ($item) {
                return "　{$item->name} WEB: {$item->web_count} - DB: {$item->count}";
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
                return implode("\n", array_merge(["【{$key}】"], array_map(function ($value) {
                    return $value;
                }, array_values($values))));
            })->toArray()),
            '```',
        ]);
    }
}
