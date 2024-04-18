<?php
declare(strict_types=1);

namespace Gotea\Command\SubCommand;

use Cake\Core\Configure;
use Cake\Datasource\ResultSetInterface;
use Cake\Http\Client;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Gotea\Command\CrawlerTrait;
use Gotea\Model\Entity\Country;
use Symfony\Component\DomCrawler\Crawler;

class RankDiffKoreaSubCommand implements RankDiffSubCommandInterface
{
    use CrawlerTrait;

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
    public function getPlayers(Client $client, array $ranks): array
    {
        $results = [];
        $crawler = $this->getCrawler(Configure::read('App.diffUrl.korea'));

        $crawler->filter('.lvList')
            ->each(function (Crawler $node) use (&$results, $ranks): void {
                $rank = $node->filter('.lv > dl > dt > span')->text();

                $players = Hash::filter($node->filter('.players li')->each(function (Crawler $node) {
                    return trim(preg_replace("/\s+/u", '', $node->text()));
                }));

                if ($players) {
                    $results[$rank] = Hash::merge(Hash::get($results, $rank, []), $players);
                }
            });

        return $results;
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
}
