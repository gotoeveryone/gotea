<?php
declare(strict_types=1);

namespace Gotea\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * TableTemplatesFixture
 */
class TableTemplatesFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'title' => 'Lorem ipsum dolor sit amet',
                'content' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'created' => '2020-08-24 16:24:40',
                'created_by' => 'Lorem ip',
                'modified' => '2020-08-24 16:24:40',
                'modified_by' => 'Lorem ip',
            ],
            [
                'id' => 2,
                'title' => 'Lorem ipsum dolor sit amet',
                'content' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'created' => '2020-08-24 16:24:40',
                'created_by' => 'Lorem ip',
                'modified' => '2020-08-24 16:24:40',
                'modified_by' => 'Lorem ip',
            ],
        ];
        parent::init();
    }
}
