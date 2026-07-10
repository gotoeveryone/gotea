<?php
declare(strict_types=1);

namespace Gotea\Authenticator;

use Authentication\Authenticator\ResultInterface;
use Authentication\Authenticator\SessionAuthenticator as BaseSessionAuthenticator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Session Authenticator
 */
class SessionAuthenticator extends BaseSessionAuthenticator
{
    /**
     * @inheritDoc
     */
    public function authenticate(ServerRequestInterface $request): ResultInterface
    {
        return parent::authenticate($request);
    }

    /**
     * @inheritDoc
     */
    public function clearIdentity(ServerRequestInterface $request, ResponseInterface $response): array
    {
        return parent::clearIdentity($request, $response);
    }
}
