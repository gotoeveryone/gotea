<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cake\Log\Log;

/**
 * ログ出力ミドルウェア
 *
 * @author  Kazuki_Kamizuru
 * @since   2017/06/07
 */
class LogMiddleware
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

        $url = $request->here();

        Log::info("${url} - 開始");
        $response = $next($request, $response);
        Log::info("${url} - 終了");

        return $response;
    }
}
