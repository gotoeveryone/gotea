<?php
use Migrations\AbstractSeed;

/**
 * Country seed.
 */
class CountrySeed extends AbstractSeed
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
    public function run()
    {
        $data = [
            [
                'code' => 'jp',
                'name' => '日本',
                'name_english' => 'Japan',
                'has_title' => true,
            ],
            [
                'code' => 'kr',
                'name' => '韓国',
                'name_english' => 'Korea',
                'has_title' => true,
            ],
            [
                'code' => 'cn',
                'name' => '中国',
                'name_english' => 'China',
                'has_title' => true,
            ],
            [
                'code' => 'tw',
                'name' => '台湾',
                'name_english' => 'Taiwan',
                'has_title' => true,
            ],
            [
                'code' => 'wr',
                'name' => '国際',
                'name_english' => 'Worlds',
                'has_title' => false,
            ],
        ];

        $table = $this->table('countries');
        $table->insert($data)->save();
    }
}
