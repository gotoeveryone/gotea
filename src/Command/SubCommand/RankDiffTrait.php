<?php
declare(strict_types=1);

namespace Gotea\Command\SubCommand;

use Cake\Http\Exception\HttpException;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

trait RankDiffTrait
{
    /**
     * URLからCrawlerオブジェクトを返却
     *
     * @param string $url URL
     * @return \Symfony\Component\DomCrawler\Crawler
     * @throws \Cake\Http\Exception\HttpException
     */
    private function getCrawler(string $url): Crawler
    {
        $client = new Client();

        $crawler = $client->request('GET', $url);
        if ($client->getInternalResponse()->getStatusCode() >= 400) {
            throw new HttpException(
                'クロール先のページが意図しないレスポンスを返しました。',
                $client->getInternalResponse()->getStatusCode()
            );
        }

        return $crawler;
    }
}
