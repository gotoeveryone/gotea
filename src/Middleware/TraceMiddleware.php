<?php
declare(strict_types=1);

namespace Gotea\Middleware;

use Cake\Log\Log;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Recording access to action.
 */
class TraceMiddleware implements MiddlewareInterface
{
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $url = $request->getRequestTarget();
        $controller = $request->getParam('controller');
        $action = $request->getParam('action');
        $message = "{$url} ({$controller}@{$action})";

        Log::debug("{$message} - Start");
        $response = $handler->handle($request);
        Log::debug("{$message} - End");

        return $response;
    }
}
