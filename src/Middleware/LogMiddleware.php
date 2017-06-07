<?php

namespace App\Middleware;

use Psr\Log\LogLevel;
use Cake\Log\LogTrait;
use Cake\Http\ServerRequest as Request;
use Cake\Http\Response;

/**
 * ログ出力ミドルウェア
 *
 * @author  Kazuki_Kamizuru
 * @since   2017/06/07
 */
class LogMiddleware
{
    use LogTrait;

    public function __invoke(Request $request, Response $response, $next)
    {
        $url = $request->here();
        $this->log("${url} - 開始", LogLevel::INFO);
        $response = $next($request, $response);
        $this->log("${url} - 終了", LogLevel::INFO);

        return $response;
    }
}
