<?php
namespace Gotea\Test\Fixture;

use Cake\I18n\FrozenTime;
use Cake\TestSuite\Fixture\TestFixture;

/**
 * CountriesFixture
 */
class CountriesFixture extends TestFixture
{
    /**
     * @inheritDoc
     */
    public function init(): void
    {
        $countries = [
            [
                'id' => 1,
                'code' => 'jp',
                'name' => '日本',
                'name_english' => 'Japan',
                'has_title' => true,
            ],
            [
                'id' => 2,
                'code' => 'kr',
                'name' => '韓国',
                'name_english' => 'Korea',
                'has_title' => true,
            ],
            [
                'id' => 3,
                'code' => 'cn',
                'name' => '中国',
                'name_english' => 'China',
                'has_title' => true,
            ],
            [
                'id' => 4,
                'code' => 'tw',
                'name' => '台湾',
                'name_english' => 'Taiwan',
                'has_title' => true,
            ],
            [
                'id' => 5,
                'code' => 'wr',
                'name' => '国際',
                'name_english' => 'Worlds',
                'has_title' => false,
            ],
        ];
        $now = FrozenTime::now();

        foreach ($countries as $country) {
            $this->records[] = [
                'id' => $country['id'],
                'code' => $country['code'],
                'name' => $country['name'],
                'name_english' => $country['name_english'],
                'has_title' => $country['has_title'],
                'created' => $now,
                'modified' => $now,
            ];
        }

        parent::init();
    }
}
