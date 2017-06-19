<?php
use Migrations\AbstractSeed;

/**
 * Rank seed.
 */
class RankSeed extends AbstractSeed
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
                'name' => '九段',
                'rank_numeric' => 9,
            ],
            [
                'name' => '八段',
                'rank_numeric' => 8,
            ],
            [
                'name' => '七段',
                'rank_numeric' => 7,
            ],
            [
                'name' => '六段',
                'rank_numeric' => 6,
            ],
            [
                'name' => '五段',
                'rank_numeric' => 5,
            ],
            [
                'name' => '四段',
                'rank_numeric' => 4,
            ],
            [
                'name' => '三段',
                'rank_numeric' => 3,
            ],
            [
                'name' => '二段',
                'rank_numeric' => 2,
            ],
            [
                'name' => '初段',
                'rank_numeric' => 1,
            ],
            [
                'name' => 'アマ',
                'rank_numeric' => 0,
            ],
        ];

        $table = $this->table('ranks');
        $table->insert($data)->save();
    }
}
