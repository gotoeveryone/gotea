<?php
declare(strict_types=1);

namespace Gotea\Middleware;

use Cake\Log\Log;

/**
 * Recording access to action.
 */
class TraceMiddleware
{
    /**
     * Invoke this middleware.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request HTTP reqeust
     * @param \Psr\Http\Message\ResponseInterface $response HTTP response
     * @param callable $next Next function
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next): ResponseInterface
    {
        $url = $request->getRequestTarget();
        $controller = $request->getParam('controller');
        $action = $request->getParam('action');
        $message = "${url} (${controller}@${action})";

        Log::debug("${message} - Start");
        $response = $next($request, $response);
        Log::debug("${message} - End");

        return $response;
    }
}
