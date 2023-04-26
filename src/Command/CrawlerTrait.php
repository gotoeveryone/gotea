<?php
declare(strict_types=1);

namespace Gotea\Command;

use Cake\Http\Exception\HttpException;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

trait CrawlerTrait
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
        $browser = new HttpBrowser(HttpClient::create());

        $crawler = $browser->request('GET', $url);
        if ($browser->getInternalResponse()->getStatusCode() >= 400) {
            throw new HttpException(
                'クロール先のページが意図しないレスポンスを返しました。',
                $browser->getInternalResponse()->getStatusCode()
            );
        }

        return $crawler;
    }
}
