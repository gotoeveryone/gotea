<?php
namespace Gotea\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * TitlesFixture
 *
 */
class TitlesFixture extends TestFixture
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
        'name' => ['type' => 'string', 'length' => 30, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'タイトル名', 'precision' => null, 'fixed' => null],
        'name_english' => ['type' => 'string', 'length' => 30, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'タイトル名（英語）', 'precision' => null, 'fixed' => null],
        'holding' => ['type' => 'integer', 'length' => 3, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '期', 'precision' => null, 'autoIncrement' => null],
        'sort_order' => ['type' => 'integer', 'length' => 2, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '並び順', 'precision' => null, 'autoIncrement' => null],
        'html_file_name' => ['type' => 'string', 'length' => 10, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'htmlファイル名', 'precision' => null, 'fixed' => null],
        'html_file_modified' => ['type' => 'date', 'length' => null, 'null' => false, 'default' => null, 'comment' => 'htmlファイル修正日', 'precision' => null],
        'remarks' => ['type' => 'string', 'length' => 500, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'その他備考', 'precision' => null, 'fixed' => null],
        'is_team' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '0', 'comment' => '団体戦判定', 'precision' => null],
        'is_closed' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '0', 'comment' => '終了済', 'precision' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '初回登録日時', 'precision' => null],
        'created_by' => ['type' => 'string', 'length' => 10, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '初回登録者', 'precision' => null, 'fixed' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '更新日時', 'precision' => null],
        'modified_by' => ['type' => 'string', 'length' => 10, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '最終更新者', 'precision' => null, 'fixed' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'uq_title' => ['type' => 'unique', 'columns' => ['country_id', 'name', 'html_file_name', 'is_closed'], 'length' => []],
            'fk_title_to_country' => ['type' => 'foreign', 'columns' => ['country_id'], 'references' => ['countries', 'id'], 'update' => 'restrict', 'delete' => 'restrict', 'length' => []],
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
            'name' => 'Lorem ipsum dolor sit amet',
            'name_english' => 'Lorem ipsum dolor sit amet',
            'holding' => 1,
            'sort_order' => 1,
            'html_file_name' => 'Lorem ip',
            'html_file_modified' => '2017-11-26',
            'remarks' => 'Lorem ipsum dolor sit amet',
            'is_team' => 1,
            'is_closed' => 1,
            'created' => '2017-11-26 14:35:16',
            'created_by' => 'Lorem ip',
            'modified' => '2017-11-26 14:35:16',
            'modified_by' => 'Lorem ip'
        ],
    ];
}
