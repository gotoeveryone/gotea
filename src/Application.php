<?php

namespace Gotea;

use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\BaseApplication;
use Cake\Http\Middleware\CsrfProtectionMiddleware;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;

/**
 * アプリケーションクラス
 *
 * @author  Kazuki Kamizuru
 * @since   2017/06/07
 */
class Application extends BaseApplication
{
    /**
     * {@inheritDoc}
     */
    public function middleware($middlewareQueue)
    {
        $middlewareQueue
            // Catch any exceptions in the lower layers,
            // and make an error page/response
            ->add(ErrorHandlerMiddleware::class)
            // Handle plugin/theme assets like CakePHP normally does.
            ->add(AssetMiddleware::class)
            // Add routing middleware.
            ->add(new RoutingMiddleware($this))
            ->add(new CsrfProtectionMiddleware());

        return $middlewareQueue;
    }
}
