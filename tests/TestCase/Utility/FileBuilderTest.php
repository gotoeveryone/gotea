<?php
declare(strict_types=1);

namespace Gotea\Test\TestCase\Utility;

use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use Gotea\Utility\FileBuilder;

/**
 * Gotea\Utility\FileBuilder Test Case
 */
class FileBuilderTest extends TestCase
{
    /**
     * Test setParentDir method
     *
     * @return void
     */
    public function testSetParentDir(): void
    {
        $builder = FileBuilder::new();

        $builder->setParentDir('/123');
        $this->assertStringContainsString('/123', $builder->getDir());

        $builder->setParentDir('/hoge/fuga');
        $this->assertStringContainsString('/hoge/fuga', $builder->getDir());
    }

    /**
     * Test output method
     *
     * @return void
     */
    public function testOutput(): void
    {
        $builder = FileBuilder::new();

        // ファイル名にディレクトリセパレータ (/) を含む場合は書き込みに失敗する
        $this->assertFalse($builder->output('hoge/fuga', 'test'));

        $this->assertTrue($builder->output('hoge', 'test'));
        $this->assertTrue(file_exists(Configure::read('App.jsonDir') . DS . 'hoge.json'));

        // 親ディレクトリの指定あり
        $builder->setParentDir('/123');
        $this->assertTrue($builder->output('fuga', 'test'));
        $this->assertTrue(file_exists(Configure::read('App.jsonDir') . DS . '/123' . DS . 'fuga.json'));
    }
}
