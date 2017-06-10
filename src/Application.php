<?php

namespace App;

use Cake\Core\Configure;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\BaseApplication;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;
use App\Middleware\LogMiddleware;

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
            ->add(new ErrorHandlerMiddleware())
            ->add(new AssetMiddleware())
            ->add(new RoutingMiddleware());
 
        if (Configure::read('debug')) {
            $middlewareQueue->add(new LogMiddleware());            
        }

        return $middlewareQueue;
    }
}