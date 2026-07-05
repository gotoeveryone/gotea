<?php
declare(strict_types=1);

namespace Gotea\Test\TestCase\Utility;

use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use Gotea\Utility\FileBuilder;
use InvalidArgumentException;

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

        $builder->setParentDir('123');
        $this->assertStringContainsString(DS . '123', $builder->getDir());

        $builder->setParentDir('hoge');
        $this->assertStringContainsString(DS . 'hoge', $builder->getDir());

        $builder->setParentDirPath('hoge', 'fuga');
        $this->assertStringContainsString(DS . 'hoge' . DS . 'fuga', $builder->getDir());
    }

    /**
     * Test setParentDir method with invalid path segments
     *
     * @return void
     */
    public function testSetParentDirFailure(): void
    {
        $this->expectException(InvalidArgumentException::class);

        FileBuilder::new()->setParentDir('../123');
    }

    /**
     * Test output method
     *
     * @return void
     */
    public function testOutput(): void
    {
        $builder = FileBuilder::new();

        $this->assertTrue($builder->output('hoge', 'test'));
        $this->assertTrue(file_exists(Configure::read('App.jsonDir') . DS . 'hoge.json'));

        // 親ディレクトリの指定あり
        $builder->setParentDir('123');
        $this->assertTrue($builder->output('fuga', 'test'));
        $this->assertTrue(file_exists(Configure::read('App.jsonDir') . DS . '123' . DS . 'fuga.json'));
    }

    /**
     * Test output method with invalid filenames
     *
     * @return void
     */
    public function testOutputFailure(): void
    {
        $builder = FileBuilder::new();

        $this->assertFalse($builder->output('', 'test'));
        $this->assertFalse($builder->output(' ', 'test'));
        $this->assertFalse($builder->output('..', 'test'));
        $this->assertFalse($builder->output('../hoge', 'test'));
        $this->assertFalse($builder->output('hoge/fuga', 'test'));
        $this->assertFalse($builder->output('hoge\\fuga', 'test'));
    }

    /**
     * Test output method with symlink parent directory
     *
     * @return void
     */
    public function testOutputFailureWithSymlinkParentDir(): void
    {
        $originalJsonDir = Configure::read('App.jsonDir');
        $baseDir = TMP . 'file_builder_' . uniqid();
        $outsideDir = TMP . 'file_builder_outside_' . uniqid();
        $linkPath = $baseDir . DS . 'ranking';

        mkdir($baseDir);
        mkdir($outsideDir);
        if (!symlink($outsideDir, $linkPath)) {
            rmdir($outsideDir);
            rmdir($baseDir);

            $this->markTestSkipped('Failed to create symlink.');
        }

        Configure::write('App.jsonDir', $baseDir);
        try {
            $builder = FileBuilder::new()->setParentDirPath('ranking', 'jp');

            $this->assertFalse($builder->output('japan2026', 'test'));
            $this->assertFalse(file_exists($outsideDir . DS . 'jp'));
        } finally {
            Configure::write('App.jsonDir', $originalJsonDir);
            if (file_exists($linkPath) || is_link($linkPath)) {
                unlink($linkPath);
            }
            if (is_dir($outsideDir)) {
                rmdir($outsideDir);
            }
            if (is_dir($baseDir)) {
                rmdir($baseDir);
            }
        }
    }
}
