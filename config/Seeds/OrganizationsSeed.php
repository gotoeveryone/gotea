<?php
use Cake\ORM\TableRegistry;
use Migrations\AbstractSeed;

/**
 * Organizations seed.
 */
class OrganizationsSeed extends AbstractSeed
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
        $countries = TableRegistry::get('Countries');
        $data = [
            [
                'country_id' => $countries->findByName('日本')->first()->id,
                'name' => '日本棋院',
                'created' => '2016-06-18 15:57:24',
                'modified' => '2016-06-18 15:57:24',
            ],
            [
                'country_id' => $countries->findByName('日本')->first()->id,
                'name' => '関西棋院',
                'created' => '2016-06-18 15:57:24',
                'modified' => '2016-06-18 15:57:24',
            ],
            [
                'country_id' => $countries->findByName('韓国')->first()->id,
                'name' => '韓国棋院',
                'created' => '2016-06-18 15:57:24',
                'modified' => '2016-06-18 15:57:24',
            ],
            [
                'country_id' => $countries->findByName('中国')->first()->id,
                'name' => '中国棋院',
                'created' => '2016-06-18 15:57:24',
                'modified' => '2016-06-18 15:57:24',
            ],
            [
                'country_id' => $countries->findByName('台湾')->first()->id,
                'name' => '台湾棋院',
                'created' => '2016-06-18 15:57:24',
                'modified' => '2016-06-18 15:57:24',
            ],
            [
                'country_id' => $countries->findByName('台湾')->first()->id,
                'name' => '中国囲棋会',
                'created' => '2016-06-18 15:57:24',
                'modified' => '2016-06-18 15:57:24',
            ],
        ];

        $table = $this->table('organizations');
        $table->insert($data)->save();
    }
}
