<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Core\Plugin;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Gotoeveryone\Middleware\TraceMiddleware;
use Gotoeveryone\Middleware\TransactionMiddleware;

/**
 * The default class to use for all routes
 *
 * The following route classes are supplied with CakePHP and are appropriate
 * to set as the default:
 *
 * - Route
 * - InflectedRoute
 * - DashedRoute
 *
 * If no call is made to `Router::defaultRouteClass()`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 *
 */
Router::defaultRouteClass('DashedRoute');

Router::scope('/', function (RouteBuilder $routes) {
    // ミドルウェアの登録
    $routes->registerMiddleware('trace', new TraceMiddleware())
        ->registerMiddleware('transaction', new TransactionMiddleware())
        ->applyMiddleware('trace', 'transaction');

    $routes->scope('/', ['controller' => 'Users'], function (RouteBuilder $routes) {
        $routes->get('/', ['action' => 'index'], 'top');
        $routes->post('/login', ['action' => 'login'], 'login');
        $routes->get('/logout', ['action' => 'logout'], 'logout');
    });

    $routes->scope('/players', ['controller' => 'Players'], function (RouteBuilder $routes) {
        $routes->get('/', ['action' => 'index'], 'players');
        $routes->post('/', ['action' => 'search'], 'find_players');
        $routes->get('/new', ['action' => 'new'], 'new_player');
        $routes->post('/save', ['action' => 'save'], 'create_player');
        $routes->get('/:id', ['action' => 'view'], 'view_player')
            ->setPatterns(['id' => RouteBuilder::ID])->setPass(['id']);
        $routes->put('/save/:id', ['action' => 'save'], 'update_player')
            ->setPatterns(['id' => RouteBuilder::ID])->setPass(['id']);

        $routes->get('/ranking', ['action' => 'ranking'], 'ranking');
    });

    $routes->scope('/players', function (RouteBuilder $routes) {
        $routes->post(
            '/:id/scores',
            ['controller' => 'TitleScores', 'action' => 'searchByPlayer'],
            'find_player_scores'
        )->setPatterns(['id' => RouteBuilder::ID])->setPass(['id']);
        $routes->post(
            '/:id/ranks',
            ['controller' => 'PlayerRanks', 'action' => 'create'],
            'create_ranks'
        )->setPatterns(['id' => RouteBuilder::ID])->setPass(['id']);
        $routes->put(
            '/:id/ranks/:rowId',
            ['controller' => 'PlayerRanks', 'action' => 'update'],
            'update_ranks'
        )->setPatterns([
            'id' => RouteBuilder::ID, 'rowId' => RouteBuilder::ID,
        ])->setPass(['id', 'rowId']);
    });

    $routes->scope('/ranks', ['controller' => 'PlayerRanks'], function (RouteBuilder $routes) {
        $routes->get('/', ['action' => 'index'], 'ranks');
    });

    // タイトル
    $routes->scope('/titles', ['controller' => 'Titles'], function (RouteBuilder $routes) {
        $routes->get('/', ['action' => 'index'], 'titles');
        $routes->get('/:id', ['action' => 'view'], 'view_title')
            ->setPatterns(['id' => RouteBuilder::ID])->setPass(['id']);
        $routes->put('/:id', ['action' => 'update'], 'update_title')
            ->setPatterns(['id' => RouteBuilder::ID])->setPass(['id']);

        // タイトル
        $routes->scope('/:id/histories', ['controller' => 'RetentionHistories'], function (RouteBuilder $routes) {
            $routes->post('/', ['action' => 'save'], 'save_histories')
                ->setPass(['id']);
        });
    });

    // タイトル成績
    $routes->scope('/scores', ['controller' => 'TitleScores'], function (RouteBuilder $routes) {
        $routes->get('/', ['action' => 'index'], 'scores');
        $routes->post('/', ['action' => 'search'], 'find_scores');
        $routes->put('/:id', ['action' => 'update'], 'update_scores')
            ->setPatterns(['id' => RouteBuilder::ID])->setPass(['id']);
        $routes->delete('/:id', ['action' => 'delete'], 'delete_scores')
            ->setPatterns(['id' => RouteBuilder::ID])->setPass(['id']);
    });

    // クエリ実行
    $routes->scope('/queries', ['controller' => 'NativeQuery'], function (RouteBuilder $routes) {
        $routes->get('/', ['action' => 'index'], 'queries');
        $routes->post('/', ['action' => 'execute'], 'execute_queries');
    });

    // API
    $routes->prefix('Api', function (RouteBuilder $routes) {

        $routes->get('/years', ['controller' => 'Years', 'action' => 'index'], 'api_years');

        $routes->get('/countries', ['controller' => 'Countries', 'action' => 'index'], 'api_countries');

        $routes->scope('/players', ['controller' => 'Players'], function (RouteBuilder $routes) {
            $routes->post('/', ['action' => 'search'], 'api_players');
            $routes->get('/ranking/:country/:year/:offset', ['controller' => 'Players', 'action' => 'searchRanking'], 'api_ranking')
                ->setPatterns(['year' => RouteBuilder::ID, 'offset' => RouteBuilder::ID])
                ->setPass(['country', 'year', 'offset']);
            $routes->post('/ranking/:country/:year/:offset', ['action' => 'createRanking'], 'api_create_ranking')
                ->setPatterns(['year' => RouteBuilder::ID, 'offset' => RouteBuilder::ID])
                ->setPass(['country', 'year', 'offset']);
            $routes->get('/ranks/:country_id', ['action' => 'searchRanks'], 'api_ranks')
                ->setPatterns(['country_id' => RouteBuilder::ID])->setPass(['country_id']);
        });

        $routes->scope('/titles', ['controller' => 'Titles'], function (RouteBuilder $routes) {
            $routes->get('/', ['action' => 'index'], 'api_titles');
            $routes->post('/', ['action' => 'create'], 'api_create_titles');
            $routes->put('//:id', ['action' => 'update'], 'api_update_titles')
                ->setPatterns(['id' => RouteBuilder::ID])->setPass(['id']);
            $routes->post('/news', ['action' => 'createNews'], 'api_news');
        });

        $routes->scope('/histories', ['controller' => 'RetentionHistories'], function (RouteBuilder $routes) {
            $routes->get('/:id', ['action' => 'view'], 'api_history')
            ->setPatterns(['id' => RouteBuilder::ID])->setPass(['id']);
        });
    });

    // フォールバックメソッド
    // $routes->fallbacks('DashedRoute');
});

/**
 * Load all plugin routes.  See the Plugin documentation on
 * how to customize the loading of plugin routes.
 */
Plugin::routes();
