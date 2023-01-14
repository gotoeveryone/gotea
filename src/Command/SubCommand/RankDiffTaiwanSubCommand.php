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

class RankDiffTaiwanSubCommand implements RankDiffSubCommandInterface
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
        $results = [];
        $rank = null;
        $crawler = $this->getCrawler(Configure::read('App.diffUrl.taiwan'));
        $crawler->filter('.post-body.entry-content div:first-child > div')
            ->each(function (Crawler $node) use (&$results, &$rank, $ranks): void {
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
     * @inheritDoc
     */
    public function getRankSummary(): ResultSetInterface
    {
        /** @var \Gotea\Model\Table\OrganizationsTable $organizations */
        $organizations = TableRegistry::getTableLocator()->get('Organizations');

        // 台湾の場合は台湾棋院のみを対象とする
        $organization = $organizations->findByName('台湾棋院')->first();

        /** @var \Gotea\Model\Table\PlayersTable $players */
        $players = TableRegistry::getTableLocator()->get('Players');

        return $players->findRanksCount($this->country->id, $organization->id)->all();
    }
}
