<?php
use Migrations\AbstractSeed;

/**
 * Ranks seed.
 */
class RanksSeed extends AbstractSeed
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
                'id' => 1,
                'name' => '初段',
                'rank_numeric' => 1,
                'created' => '2017-12-24 20:24:24',
                'modified' => '2017-12-24 20:24:24',
            ],
            [
                'id' => 2,
                'name' => '二段',
                'rank_numeric' => 2,
                'created' => '2017-12-24 20:24:24',
                'modified' => '2017-12-24 20:24:24',
            ],
            [
                'id' => 3,
                'name' => '三段',
                'rank_numeric' => 3,
                'created' => '2017-12-24 20:24:24',
                'modified' => '2017-12-24 20:24:24',
            ],
            [
                'id' => 4,
                'name' => '四段',
                'rank_numeric' => 4,
                'created' => '2017-12-24 20:24:24',
                'modified' => '2017-12-24 20:24:24',
            ],
            [
                'id' => 5,
                'name' => '五段',
                'rank_numeric' => 5,
                'created' => '2017-12-24 20:24:24',
                'modified' => '2017-12-24 20:24:24',
            ],
            [
                'id' => 6,
                'name' => '六段',
                'rank_numeric' => 6,
                'created' => '2017-12-24 20:24:24',
                'modified' => '2017-12-24 20:24:24',
            ],
            [
                'id' => 7,
                'name' => '七段',
                'rank_numeric' => 7,
                'created' => '2017-12-24 20:24:24',
                'modified' => '2017-12-24 20:24:24',
            ],
            [
                'id' => 8,
                'name' => '八段',
                'rank_numeric' => 8,
                'created' => '2017-12-24 20:24:24',
                'modified' => '2017-12-24 20:24:24',
            ],
            [
                'id' => 9,
                'name' => '九段',
                'rank_numeric' => 9,
                'created' => '2017-12-24 20:24:24',
                'modified' => '2017-12-24 20:24:24',
            ],
            [
                'id' => 0,
                'name' => 'アマ',
                'rank_numeric' => 0,
                'created' => '2017-12-24 20:24:24',
                'modified' => '2017-12-24 20:24:24',
            ],
        ];

        $table = $this->table('ranks');
        $table->insert($data)->save();
    }
}
