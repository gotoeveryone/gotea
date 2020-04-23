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
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => 'サロゲートキー', 'autoIncrement' => true, 'precision' => null],
        'country_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '国ID', 'precision' => null, 'autoIncrement' => null],
        'name' => ['type' => 'string', 'length' => 20, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '組織名', 'precision' => null, 'fixed' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '初回登録日時', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '更新日時', 'precision' => null],
        '_indexes' => [
            'fk_organization_to_country' => ['type' => 'index', 'columns' => ['country_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'uq_name' => ['type' => 'unique', 'columns' => ['name'], 'length' => []],
            'fk_organization_to_country' => ['type' => 'foreign', 'columns' => ['country_id'], 'references' => ['countries', 'id'], 'update' => 'restrict', 'delete' => 'restrict', 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * @inheritDoc
     */
    public function init()
    {
        $organizations = [
            [
                'country_id' => 1,
                'name' => '日本棋院',
            ],
            [
                'country_id' => 1,
                'name' => '関西棋院',
            ],
            [
                'country_id' => 2,
                'name' => '韓国棋院',
            ],
            [
                'country_id' => 3,
                'name' => '中国棋院',
            ],
            [
                'country_id' => 4,
                'name' => '台湾棋院',
            ],
            [
                'country_id' => 4,
                'name' => '中国囲棋協会',
            ],
        ];
        $now = FrozenTime::now();

        foreach ($organizations as $organization) {
            $this->records[] = [
                'name' => $organization['name'],
                'country_id' => $organization['country_id'],
                'created' => $now,
                'modified' => $now,
            ];
        }

        parent::init();
    }
}
