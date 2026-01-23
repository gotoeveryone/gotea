<?php
declare(strict_types=1);

namespace Gotea\Test\TestCase;

use Authentication\Middleware\AuthenticationMiddleware;
use Authorization\Middleware\AuthorizationMiddleware;
use Cake\Core\Configure;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\Middleware\BodyParserMiddleware;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;
use Cake\TestSuite\TestCase;
use Gotea\Application;
use Gotea\Middleware\TraceMiddleware;
use Gotea\Middleware\TransactionMiddleware;
use InvalidArgumentException;

/**
 * ApplicationTest class
 */
class ApplicationTest extends TestCase
{
    /**
     * testBootstrap
     *
     * @return void
     */
    public function testBootstrap()
    {
        $app = new Application(dirname(dirname(__DIR__)) . '/config');
        $app->bootstrap();
        $plugins = $app->getPlugins();

        $this->assertCount(7, $plugins);
        $this->assertSame('Bake', $plugins->get('Bake')->getName());
        $this->assertSame('Migrations', $plugins->get('Migrations')->getName());
        $this->assertSame('Cake/Repl', $plugins->get('Cake/Repl')->getName());
        $this->assertSame('DebugKit', $plugins->get('DebugKit')->getName());
        $this->assertSame('Shim', $plugins->get('Shim')->getName());
        $this->assertSame('Authentication', $plugins->get('Authentication')->getName());
        $this->assertSame('Authorization', $plugins->get('Authorization')->getName());
        $this->assertFalse($plugins->has('CakeSentry'));

        Configure::write('Sentry.dsn', 'hogefuga');
        $app->bootstrap();
        $plugins = $app->getPlugins();
        $this->assertCount(8, $plugins);
        $this->assertTrue($plugins->has('CakeSentry'));
    }

    /**
     * testBootstrapPluginWitoutHalt
     *
     * @return void
     */
    public function testBootstrapPluginWithoutHalt()
    {
        $this->expectException(InvalidArgumentException::class);

        $app = $this->getMockBuilder(Application::class)
            ->setConstructorArgs([dirname(dirname(__DIR__)) . '/config'])
            ->onlyMethods(['addPlugin'])
            ->getMock();

        $app->method('addPlugin')
            ->will($this->throwException(new InvalidArgumentException('test exception.')));

        /** @var \Gotea\Application $app */
        $app->bootstrap();
    }

    /**
     * testMiddleware
     *
     * @return void
     */
    public function testMiddleware()
    {
        $app = new Application(dirname(dirname(__DIR__)) . '/config');
        $middleware = new MiddlewareQueue();

        $middleware = $app->middleware($middleware);

        $assertMiddlewares = [
            ErrorHandlerMiddleware::class,
            AssetMiddleware::class,
            RoutingMiddleware::class,
            BodyParserMiddleware::class,
            AuthenticationMiddleware::class,
            AuthorizationMiddleware::class,
            TraceMiddleware::class,
            TransactionMiddleware::class,
        ];
        $this->assertEquals(count($assertMiddlewares), $middleware->count());
        foreach ($assertMiddlewares as $idx => $target) {
            $middleware->seek($idx);
            $this->assertInstanceOf($target, $middleware->current());
        }
    }
}
