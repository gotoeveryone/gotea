<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LogLevel;
use Cake\Log\LogTrait;

/**
 * ログ出力ミドルウェア
 *
 * @author  Kazuki_Kamizuru
 * @since   2017/06/07
 */
class LogMiddleware
{
    use LogTrait;

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        $url = $request->here();
        $this->log("${url} - 開始", LogLevel::INFO);
        $response = $next($request, $response);
        $this->log("${url} - 終了", LogLevel::INFO);

        return $response;
    }
}
