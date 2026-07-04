<?php
declare(strict_types=1);

namespace Gotea\Test\TestCase\View\Helper;

use Cake\TestSuite\TestCase;
use Cake\View\View;
use Gotea\View\Helper\HtmlHelper;

/**
 * HtmlHelper Test Case
 */
class HtmlHelperTest extends TestCase
{
    private HtmlHelper $helper;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->helper = new HtmlHelper(new View());
    }

    /**
     * プラグインアセットは Vite manifest の同名エントリに解決されないこと
     *
     * @return void
     */
    public function testScriptDoesNotResolvePluginAssetAsViteAsset(): void
    {
        $html = $this->helper->script('DebugKit./js/main', ['type' => 'module']);

        $this->assertStringContainsString('src="/debug_kit/js/main.js', $html);
    }
}
