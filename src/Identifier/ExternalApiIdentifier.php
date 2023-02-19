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
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         1.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Gotea\Identifier;

use Authentication\Identifier\AbstractIdentifier;
use Authentication\Identifier\Resolver\ResolverAwareTrait;

/**
 * 外部 API 認証を行うクラス
 */
class ExternalApiIdentifier extends AbstractIdentifier
{
    use ResolverAwareTrait;

    /**
     * Default configuration.
     * - `fields` The fields to use to identify a user by:
     *   - `username`: one or many username fields.
     *   - `password`: password field.
     * - `resolver` The resolver implementation to use.
     * - `passwordHasher` Password hasher class. Can be a string specifying class name
     *    or an array containing `className` key, any other keys will be passed as
     *    config to the class. Defaults to 'Default'.
     *
     * @var array
     */
    protected array $_defaultConfig = [
        'fields' => [
            self::CREDENTIAL_USERNAME => 'account',
            self::CREDENTIAL_PASSWORD => 'password',
        ],
        'resolver' => 'Gotea.Api',
    ];

    /**
     * @inheritDoc
     */
    public function identify(array $data)
    {
        if (!isset($data[self::CREDENTIAL_USERNAME])) {
            return null;
        }

        $usernameKey = $this->getConfig('fields.' . self::CREDENTIAL_USERNAME);
        $passwordKey = $this->getConfig('fields.' . self::CREDENTIAL_PASSWORD);

        return $this->getResolver()->find([
            $usernameKey => $data[self::CREDENTIAL_USERNAME],
            $passwordKey => $data[self::CREDENTIAL_PASSWORD],
        ]);
    }
}
