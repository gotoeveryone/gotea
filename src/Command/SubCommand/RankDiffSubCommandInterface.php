<?php
declare(strict_types=1);

namespace Gotea\Command\SubCommand;

use Cake\Datasource\ResultSetInterface;
use Cake\Http\Client as HttpClient;

interface RankDiffSubCommandInterface
{
    /**
     * 棋士情報を取得する
     *
     * @param \Cake\Http\Client $client HTTP クライアント
     * @param array $ranks 段位一覧
     * @return array 段位と棋士の一覧
     */
    public function getPlayers(HttpClient $client, array $ranks): array;

    /**
     * 段位ごとの集計結果を取得する
     *
     * @return \Cake\Datasource\ResultSetInterface 段位の集計結果
     */
    public function getRankSummary(): ResultSetInterface;
}
