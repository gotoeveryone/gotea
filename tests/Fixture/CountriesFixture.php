<?php
namespace Gotea\Test\Fixture;

use Cake\I18n\FrozenTime;
use Cake\TestSuite\Fixture\TestFixture;

/**
 * CountriesFixture
 *
 */
class CountriesFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => 'サロゲートキー', 'autoIncrement' => true, 'precision' => null],
        'code' => ['type' => 'string', 'length' => 2, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '国名コード（ラテン文字2文字）', 'precision' => null, 'fixed' => null],
        'name' => ['type' => 'string', 'length' => 20, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '国名', 'precision' => null, 'fixed' => null],
        'name_english' => ['type' => 'string', 'length' => 30, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '国名（英語）', 'precision' => null, 'fixed' => null],
        'has_title' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '0', 'comment' => '所属棋士有無', 'precision' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '初回登録日時', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '更新日時', 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'uq_name' => ['type' => 'unique', 'columns' => ['name'], 'length' => []],
            'uq_name_english' => ['type' => 'unique', 'columns' => ['name_english'], 'length' => []],
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
        $countries = [
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
        $now = FrozenTime::now();

        foreach ($countries as $country) {
            $this->records[] = [
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
