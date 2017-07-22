<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LogLevel;
use Cake\Datasource\ConnectionManager;
use Cake\Log\LogTrait;

/**
 * トランザクション管理ミドルウェア
 *
 * @author  Kazuki_Kamizuru
 * @since   2017/07/20
 */
class TransactionMiddleware
{
    use LogTrait;

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        $conn = ConnectionManager::get('default');
        return $conn->enableSavePoints(true)
            ->transactional(function() use ($request, $response, $next) {
                try {
                    $res = $next($request, $response);
                    $this->log('トランザクションをコミットしました。', LogLevel::INFO);
                    return $res;
                } catch (Exception $e) {
                    $this->log('トランザクションをロールバックしました。', LogLevel::ERROR);
                    throw $e;
                }
            });
    }
}
