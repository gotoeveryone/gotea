<?php
declare(strict_types=1);

namespace Gotea\Client;

use Abraham\TwitterOAuth\TwitterOAuth;
use Cake\Core\Configure;

/**
 * Twitter 用クライアント
 */
class TwitterClient
{
    private TwitterOAuth $client;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $consumerKey = Configure::read('App.twitter.consumerKey', '');
        $consumerSecret = Configure::read('App.twitter.consumerSecret', '');
        $accessToken = Configure::read('App.twitter.accessToken');
        $accessTokenSecret = Configure::read('App.twitter.accessTokenSecret');

        $this->client = new TwitterOAuth($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);
    }

    /**
     * メッセージを投稿する
     *
     * @param string $message
     * @return object|array
     */
    public function post(string $message): object|array
    {
        if (Configure::read('debug')) {
            return [];
        }

        return $this->client->post('tweets', [
            'text' => $message,
        ], true);
    }
}
