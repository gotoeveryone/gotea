<?php
declare(strict_types=1);

namespace Gotea\Middleware;

use Cake\Datasource\ConnectionManager;
use Cake\Log\Log;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Throwable;

/**
 * Manage transaction at application request unit.
 */
class TransactionMiddleware
{
    /**
     * Connection name
     *
     * @var string
     */
    private $__name = '';

    /**
     * Constructor
     *
     * @param string $name Connection name
     */
    public function __construct($name = 'default')
    {
        $this->__name = $name;
    }

    /**
     * Invoke this middleware.
     *
     * @param \Gotoeveryone\Middleware\Psr\Http\Message\ServerRequestInterface $request HTTP reqeust
     * @param \Gotoeveryone\Middleware\Psr\Http\Message\ResponseInterface $response HTTP response
     * @param callable $next Next function
     * @return \Gotoeveryone\Middleware\Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response, $next)
    {
        $conn = ConnectionManager::get($this->__name);

        return $conn->enableSavePoints(true)
            ->transactional(function ($conn) use ($request, $response, $next) {
                try {
                    $res = $next($request, $response);
                    Log::debug('Commit the transaction.');

                    return $res;
                } catch (Throwable $e) {
                    Log::error('Error! Rollback the transaction...');
                    throw $e;
                }
            });
    }
}
