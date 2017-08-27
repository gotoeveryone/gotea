<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cake\Datasource\ConnectionManager;
use Cake\Log\Log;

/**
 * トランザクション管理ミドルウェア
 *
 * @author  Kazuki_Kamizuru
 * @since   2017/07/20
 */
class TransactionMiddleware
{
    /**
     * ミドルウェアの実行メソッド
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        // プラグインのリクエストなら後続処理へ
        $params = (array) $request->getAttribute('params', []);
        if (isset($params['plugin'])) {
            return $next($request, $response);
        }

        $conn = ConnectionManager::get('default');

        return $conn->enableSavePoints(true)
            ->transactional(function($conn) use ($request, $response, $next) {
                try {
                    $res = $next($request, $response);
                    Log::debug('トランザクションをコミットしました。');
                    return $res;
                } catch (Exception $e) {
                    Log::error('トランザクションをロールバックしました。');
                    throw $e;
                }
            });
    }
}
