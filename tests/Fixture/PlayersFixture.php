<?php
namespace Gotea\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * PlayersFixture
 */
class PlayersFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => 'サロゲートキー', 'autoIncrement' => true, 'precision' => null],
        'country_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '所属国ID', 'precision' => null, 'autoIncrement' => null],
        'rank_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '段位ID', 'precision' => null, 'autoIncrement' => null],
        'organization_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '所属ID', 'precision' => null, 'autoIncrement' => null],
        'name' => ['type' => 'string', 'length' => 20, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '棋士名', 'precision' => null, 'fixed' => null],
        'name_english' => ['type' => 'string', 'length' => 40, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '棋士名（英語）', 'precision' => null, 'fixed' => null],
        'name_other' => ['type' => 'string', 'length' => 20, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '棋士名（その他）', 'precision' => null, 'fixed' => null],
        'sex' => ['type' => 'string', 'length' => 2, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '性別', 'precision' => null, 'fixed' => null],
        'joined' => ['type' => 'string', 'length' => 8, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '入段日', 'precision' => null, 'fixed' => null],
        'birthday' => ['type' => 'date', 'length' => null, 'null' => true, 'default' => null, 'comment' => '生年月日', 'precision' => null],
        'remarks' => ['type' => 'string', 'length' => 500, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'その他備考', 'precision' => null, 'fixed' => null],
        'is_retired' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '0', 'comment' => '引退済', 'precision' => null],
        'retired' => ['type' => 'date', 'length' => null, 'null' => true, 'default' => null, 'comment' => '引退日', 'precision' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '初回登録日時', 'precision' => null],
        'created_by' => ['type' => 'string', 'length' => 10, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '初回登録者', 'precision' => null, 'fixed' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '更新日時', 'precision' => null],
        'modified_by' => ['type' => 'string', 'length' => 10, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '最終更新者', 'precision' => null, 'fixed' => null],
        '_indexes' => [
            'idx_name' => ['type' => 'index', 'columns' => ['name'], 'length' => []],
            'fk_player_to_rank' => ['type' => 'index', 'columns' => ['rank_id'], 'length' => []],
            'fk_player_to_organization' => ['type' => 'index', 'columns' => ['organization_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'uq_player' => ['type' => 'unique', 'columns' => ['country_id', 'name', 'birthday'], 'length' => []],
            'fk_player_to_country' => ['type' => 'foreign', 'columns' => ['country_id'], 'references' => ['countries', 'id'], 'update' => 'restrict', 'delete' => 'restrict', 'length' => []],
            'fk_player_to_organization' => ['type' => 'foreign', 'columns' => ['organization_id'], 'references' => ['organizations', 'id'], 'update' => 'restrict', 'delete' => 'restrict', 'length' => []],
            'fk_player_to_rank' => ['type' => 'foreign', 'columns' => ['rank_id'], 'references' => ['ranks', 'id'], 'update' => 'restrict', 'delete' => 'restrict', 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 1,
            'country_id' => 1,
            'rank_id' => 1,
            'organization_id' => 1,
            'name' => 'Test Player 1',
            'name_english' => 'Lorem ipsum dolor sit amet',
            'name_other' => 'Lorem ipsum dolor ',
            'sex' => '男性',
            'joined' => '20171108',
            'birthday' => '2017-10-26',
            'remarks' => 'Lorem ipsum dolor sit amet',
            'is_retired' => 0,
            'retired' => null,
            'created' => '2017-11-26 14:32:20',
            'created_by' => 'Lorem ip',
            'modified' => '2017-11-26 14:32:20',
            'modified_by' => 'Lorem ip',
        ],
        [
            'id' => 2,
            'country_id' => 2,
            'rank_id' => 2,
            'organization_id' => 2,
            'name' => 'Test Player 2',
            'name_english' => 'Lorem ipsum dolor sit amet',
            'name_other' => 'Lorem ipsum dolor ',
            'sex' => '女性',
            'joined' => '20171108',
            'birthday' => '2017-10-26',
            'remarks' => 'Lorem ipsum dolor sit amet',
            'is_retired' => 0,
            'retired' => null,
            'created' => '2017-11-26 14:32:20',
            'created_by' => 'Lorem ip',
            'modified' => '2017-11-26 14:32:20',
            'modified_by' => 'Lorem ip',
        ],
        [
            'id' => 3,
            'country_id' => 1,
            'rank_id' => 2,
            'organization_id' => 1,
            'name' => 'Test Player 3',
            'name_english' => 'Lorem ipsum dolor sit amet',
            'name_other' => 'Lorem ipsum dolor ',
            'sex' => '男性',
            'joined' => '20171108',
            'birthday' => '2017-10-26',
            'remarks' => 'Lorem ipsum dolor sit amet',
            'is_retired' => 0,
            'retired' => null,
            'created' => '2017-11-26 14:32:20',
            'created_by' => 'Lorem ip',
            'modified' => '2017-11-26 14:32:20',
            'modified_by' => 'Lorem ip',
        ],
        [
            'id' => 4,
            'country_id' => 1,
            'rank_id' => 3,
            'organization_id' => 1,
            'name' => 'Test Player 4',
            'name_english' => 'Lorem ipsum dolor sit amet',
            'name_other' => 'Lorem ipsum dolor ',
            'sex' => '男性',
            'joined' => '20171108',
            'birthday' => '2017-10-26',
            'remarks' => 'Lorem ipsum dolor sit amet',
            'is_retired' => 0,
            'retired' => null,
            'created' => '2017-11-26 14:32:20',
            'created_by' => 'Lorem ip',
            'modified' => '2017-11-26 14:32:20',
            'modified_by' => 'Lorem ip',
        ],
    ];
}
