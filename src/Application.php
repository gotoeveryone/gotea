<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     3.3.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Gotea;

use Authentication\AuthenticationService;
use Authentication\AuthenticationServiceInterface;
use Authentication\AuthenticationServiceProviderInterface;
use Authentication\Middleware\AuthenticationMiddleware;
use Authorization\AuthorizationService;
use Authorization\AuthorizationServiceInterface;
use Authorization\AuthorizationServiceProviderInterface;
use Authorization\Exception\AuthorizationRequiredException;
use Authorization\Exception\ForbiddenException;
use Authorization\Middleware\AuthorizationMiddleware;
use Authorization\Policy\MapResolver;
use Cake\Core\Configure;
use Cake\Core\Exception\MissingPluginException;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\BaseApplication;
use Cake\Http\Middleware\BodyParserMiddleware;
use Cake\Http\Middleware\HttpsEnforcerMiddleware;
use Cake\Http\MiddlewareQueue;
use Cake\Http\ServerRequest;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;
use DebugKit\Plugin;
use Gotea\Middleware\TraceMiddleware;
use Gotea\Middleware\TransactionMiddleware;
use Gotea\Policy\RequestPolicy;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Application setup class.
 *
 * This defines the bootstrapping logic and middleware layers you
 * want to use in your application.
 */
class Application extends BaseApplication implements
    AuthenticationServiceProviderInterface,
    AuthorizationServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function bootstrap(): void
    {
        // Call parent to load bootstrap from files.
        parent::bootstrap();

        if (PHP_SAPI === 'cli') {
            $this->bootstrapCli();
        }

        /*
         * Only try to load DebugKit in development mode
         * Debug Kit should not be installed on a production system
         */
        if (Configure::read('debug')) {
            $this->addPlugin(Plugin::class);
        }

        // Load more plugins here
        $this->addPlugin('Shim');
        $this->addPlugin('Authentication');
        $this->addPlugin('Authorization');

        if (Configure::read('Sentry.dsn')) {
            $this->addPlugin('Connehito/CakeSentry');
        }
    }

    /**
     * Setup the middleware queue your application will use.
     *
     * @param \Cake\Http\MiddlewareQueue $middlewareQueue The middleware queue to setup.
     * @return \Cake\Http\MiddlewareQueue The updated middleware queue.
     */
    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        $middlewareQueue
            // Catch any exceptions in the lower layers,
            // and make an error page/response
            ->add(new ErrorHandlerMiddleware(Configure::read('Error')))

            // Handle plugin/theme assets like CakePHP normally does.
            ->add(new AssetMiddleware([
                'cacheTime' => Configure::read('Asset.cacheTime'),
            ]))

            // Add routing middleware.
            // If you have a large number of routes connected, turning on routes
            // caching in production could improve performance. For that when
            // creating the middleware instance specify the cache config name by
            // using it's second constructor argument:
            // `new RoutingMiddleware($this, '_cake_routes_')`
            ->add(new RoutingMiddleware($this))

            // Parse various types of encoded request bodies so that they are
            // available as array through $request->getData()
            // https://book.cakephp.org/4/en/controllers/middleware.html#body-parser-middleware
            ->add(new BodyParserMiddleware())

            // Add authentication middleware.
            ->add(new AuthenticationMiddleware($this, [
                'unauthenticatedRedirect' => '/',
                'queryParam' => 'redirect',
            ]))

            // Add authorization middleware.
            ->add(new AuthorizationMiddleware($this, [
                'requireAuthorizationCheck' => false,
                'unauthorizedHandler' => [
                    'className' => 'Authorization.Redirect',
                    'url' => '/',
                    'queryParam' => 'redirect',
                    'exceptions' => [
                        AuthorizationRequiredException::class,
                        ForbiddenException::class,
                    ],
                ],
            ]))

            // Add trace middleware.
            ->add(new TraceMiddleware($this))

            // Add transaction middleware.
            ->add(new TransactionMiddleware());

        // HTTPSを強制
        if (!Configure::read('debug') && Configure::read('environment') !== 'local') {
            $middlewareQueue->add(new HttpsEnforcerMiddleware([
                'redirect' => true,
            ]));
        }

        return $middlewareQueue;
    }

    /**
     * @return void
     */
    protected function bootstrapCli(): void
    {
        try {
            $this->addPlugin('Bake');

            /*
             * Only try to load DebugKit in development mode
             * Debug Kit should not be installed on a production system
             */
            if (Configure::read('debug')) {
                $this->addPlugin('Cake/Repl');
            }
        } catch (MissingPluginException $e) {
            // Do not halt if the plugin is missing
        }

        $this->addPlugin('Migrations');

        // Load more plugins here
    }

    /**
     * Returns a authentication service provider instance.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request
     * @return \Authentication\AuthenticationServiceInterface
     */
    public function getAuthenticationService(ServerRequestInterface $request): AuthenticationServiceInterface
    {
        $service = new AuthenticationService();

        $fields = [
            'username' => 'account',
            'password' => 'password',
        ];

        // Load identifiers
        $service->loadIdentifier('Authentication.Password', [
            'fields' => $fields,
        ]);
        // $service->loadIdentifier('Gotea.ExternalApi', [
        //     'fields' => $fields,
        // ]);

        // Load the authenticators, you want session first
        $service->loadAuthenticator('Gotea.Session');
        $service->loadAuthenticator('Authentication.Form', [
            'fields' => $fields,
            'loginUrl' => '/login',
        ]);

        return $service;
    }

    /**
     * Returns a authorization service provider instance.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request
     * @return \Authorization\AuthorizationService
     */
    public function getAuthorizationService(ServerRequestInterface $request): AuthorizationServiceInterface
    {
        $resolver = new MapResolver();
        $resolver->map(ServerRequest::class, RequestPolicy::class);

        return new AuthorizationService($resolver);
    }
}
