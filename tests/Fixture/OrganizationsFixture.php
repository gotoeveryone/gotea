<?php
namespace Gotea\Test\Fixture;

use Cake\I18n\FrozenTime;
use Cake\TestSuite\Fixture\TestFixture;

/**
 * OrganizationsFixture
 */
class OrganizationsFixture extends TestFixture
{
    /**
     * @inheritDoc
     */
    public function init(): void
    {
        $organizations = [
            [
                'id' => 1,
                'country_id' => 1,
                'name' => '日本棋院',
            ],
            [
                'id' => 2,
                'country_id' => 1,
                'name' => '関西棋院',
            ],
            [
                'id' => 3,
                'country_id' => 2,
                'name' => '韓国棋院',
            ],
            [
                'id' => 4,
                'country_id' => 3,
                'name' => '中国棋院',
            ],
            [
                'id' => 5,
                'country_id' => 4,
                'name' => '台湾棋院',
            ],
            [
                'id' => 6,
                'country_id' => 4,
                'name' => '中国囲棋協会',
            ],
        ];
        $now = FrozenTime::now();

        foreach ($organizations as $organization) {
            $this->records[] = [
                'id' => $organization['id'],
                'name' => $organization['name'],
                'country_id' => $organization['country_id'],
                'created' => $now,
                'modified' => $now,
            ];
        }

        parent::init();
    }
}
