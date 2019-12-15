<?php

namespace Gotea\Identifier\Resolver;

use Authentication\Identifier\Resolver\ResolverInterface;
use Cake\Core\InstanceConfigTrait;
use Cake\Log\Log;
use Cake\Utility\Hash;
use Gotea\ApiTrait;

class ApiResolver implements ResolverInterface
{
    use ApiTrait;
    use InstanceConfigTrait;

    /**
     * Default configuration.
     * - `userModel` The alias for users table, defaults to Users.
     * - `finder` The finder method to use to fetch user record. Defaults to 'all'.
     *   You can set finder name as string or an array where key is finder name and value
     *   is an array passed to `Table::find()` options.
     *   E.g. ['finderName' => ['some_finder_option' => 'some_value']]
     *
     * @var array
     */
    protected $_defaultConfig = [
    ];

    /**
     * Constructor.
     *
     * @param array $config Config array.
     */
    public function __construct(array $config = [])
    {
        $this->setConfig($config);
    }

    /**
     * {@inheritDoc}
     */
    public function find(array $conditions, $type = self::TYPE_AND)
    {
        // トークン発行
        $response = $this->callApi('auth', 'post', $conditions);

        if ($response['status'] !== 200) {
            return null;
        }

        // ユーザ取得
        $token = Hash::get($response, 'content.accessToken');
        $response = $this->callApi('users', 'get', [], [
            'Authorization' => "Bearer ${token}",
        ]);

        if (Hash::get($response, 'status') !== 200) {
            return null;
        }

        // ユーザ情報に認証関連の値を設定して返却
        $user = Hash::get($response, 'content');
        $user['password'] = $conditions['password'];
        $user['accessToken'] = $token;

        Log::info(__('User {0} is logged', Hash::get($user, 'account')));

        return $user;
    }
}
