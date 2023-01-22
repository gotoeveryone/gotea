<?php
declare(strict_types=1);

namespace Gotea\Command\SubCommand;

use Cake\Core\Configure;
use Cake\Datasource\ResultSetInterface;
use Cake\Http\Client as HttpClient;
use Cake\ORM\TableRegistry;
use Gotea\Model\Entity\Country;

class RankDiffKoreaSubCommand implements RankDiffSubCommandInterface
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
        $url = Configure::read('App.diffUrl.korea');

        return collection(array_reverse(range(1, 9)))->map(function (int $rank) use ($client, $url) {
            $response = $client->get($url, [
                'q' => "nation=1,ob_forc={$rank}",
            ]);

            return [
                'rank' => $rank,
                'players' => array_map(function ($item) {
                    return $item['prpl_name'];
                }, $response->getJson()['recordset']),
            ];
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
}
