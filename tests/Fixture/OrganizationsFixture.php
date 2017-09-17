<?php
namespace App\Test\Fixture;

use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\Fixture\TestFixture;

/**
 * OrganizationsFixture
 *
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
     * {@inheritDoc}
     */
    public function init()
    {
        // $countries = TableRegistry::get('Contries');
        $organizations = [
            [
                'country_id' => 1,
                // 'country_id' => $countries->findByName('日本')->first()->id,
                'name' => '日本棋院',
            ],
            [
                'country_id' => 1,
                // 'country_id' => $countries->findByName('日本')->first()->id,
                'name' => '関西棋院',
            ],
            [
                'country_id' => 2,
                // 'country_id' => $countries->findByName('韓国')->first()->id,
                'name' => '韓国棋院',
            ],
            [
                'country_id' => 3,
                // 'country_id' => $countries->findByName('中国')->first()->id,
                'name' => '中国棋院',
            ],
            [
                'country_id' => 4,
                // 'country_id' => $countries->findByName('台湾')->first()->id,
                'name' => '台湾棋院',
            ],
            [
                'country_id' => 4,
                // 'country_id' => $countries->findByName('台湾')->first()->id,
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
