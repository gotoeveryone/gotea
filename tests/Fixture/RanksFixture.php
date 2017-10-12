<?php
namespace Gotea\Test\Fixture;

use Cake\I18n\FrozenTime;
use Cake\TestSuite\Fixture\TestFixture;

/**
 * RanksFixture
 *
 */
class RanksFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => 'サロゲートキー', 'autoIncrement' => true, 'precision' => null],
        'name' => ['type' => 'string', 'length' => 10, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '段位', 'precision' => null, 'fixed' => null],
        'rank_numeric' => ['type' => 'integer', 'length' => 2, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '段位（数字）', 'precision' => null, 'autoIncrement' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '初回登録日時', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '更新日時', 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'uq_name' => ['type' => 'unique', 'columns' => ['name'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        $ranks = [
            [
                'number' => 1,
                'name' => '初段',
            ],
            [
                'number' => 2,
                'name' => '二段',
            ],
            [
                'number' => 3,
                'name' => '三段',
            ],
            [
                'number' => 4,
                'name' => '四段',
            ],
            [
                'number' => 5,
                'name' => '五段',
            ],
            [
                'number' => 6,
                'name' => '六段',
            ],
            [
                'number' => 7,
                'name' => '七段',
            ],
            [
                'number' => 8,
                'name' => '八段',
            ],
            [
                'number' => 9,
                'name' => '九段',
            ],
            [
                'number' => null,
                'name' => 'アマ',
            ],
        ];
        $now = FrozenTime::now();

        foreach ($ranks as $rank) {
            $this->records[] = [
                'name' => $rank['name'],
                'rank_numeric' => $rank['number'],
                'created' => $now,
                'modified' => $now,
            ];
        }

        parent::init();
    }
}
