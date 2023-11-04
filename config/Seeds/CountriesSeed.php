<?php
declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * Countries seed.
 */
class CountriesSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     *
     * @return void
     */
    public function run(): void
    {
        $data = [
            [
                'code' => 'jp',
                'name' => '日本',
                'name_english' => 'Japan',
                'has_title' => 1,
                'created' => '2017-12-24 20:24:24',
                'modified' => '2017-12-24 20:24:24',
            ],
            [
                'code' => 'kr',
                'name' => '韓国',
                'name_english' => 'Korea',
                'has_title' => 1,
                'created' => '2017-12-24 20:24:24',
                'modified' => '2017-12-24 20:24:24',
            ],
            [
                'code' => 'cn',
                'name' => '中国',
                'name_english' => 'China',
                'has_title' => 1,
                'created' => '2017-12-24 20:24:24',
                'modified' => '2017-12-24 20:24:24',
            ],
            [
                'code' => 'tw',
                'name' => '台湾',
                'name_english' => 'Taiwan',
                'has_title' => 1,
                'created' => '2017-12-24 20:24:24',
                'modified' => '2017-12-24 20:24:24',
            ],
            [
                'code' => 'wr',
                'name' => '国際',
                'name_english' => 'Worlds',
                'has_title' => 0,
                'created' => '2017-12-24 20:24:24',
                'modified' => '2017-12-24 20:24:24',
            ],
        ];

        $table = $this->table('countries');
        $table->insert($data)->save();
    }
}
