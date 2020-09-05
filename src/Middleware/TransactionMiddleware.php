<?php
declare(strict_types=1);

namespace Gotea\Middleware;

use Cake\Datasource\ConnectionManager;
use Cake\Log\Log;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

/**
 * Manage transaction at application request unit.
 */
class TransactionMiddleware implements MiddlewareInterface
{
    /**
     * Connection name
     *
     * @var string
     */
    private $name = '';

    /**
     * Constructor
     *
     * @param string $options Connection name
     */
    public function __construct(array $options = [])
    {
        $options += ['name' => 'default'];
        $this->name = $options['name'];
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $conn = ConnectionManager::get($this->name);

        return $conn->enableSavePoints(true)
            ->transactional(function ($conn) use ($request, $handler) {
                try {
                    $response = $handler->handle($request);
                    Log::debug('Commit the transaction.');

                    return $response;
                } catch (Throwable $e) {
                    Log::error('Error! Rollback the transaction...');
                    throw $e;
                }
            });
    }
}
