<?php

namespace Gotea\Authenticator;

use Authentication\Authenticator\SessionAuthenticator as BaseSessionAuthenticator;
use Gotea\ApiTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Session Authenticator
 */
class SessionAuthenticator extends BaseSessionAuthenticator
{
    use ApiTrait;

    /**
     * {@inheritDoc}
     */
    public function authenticate(ServerRequestInterface $request, ResponseInterface $response)
    {
        return parent::authenticate($request, $response);
    }

    /**
     * {@inheritDoc}
     */
    public function clearIdentity(ServerRequestInterface $request, ResponseInterface $response)
    {
        $sessionKey = $this->getConfig('sessionKey');
        /** @var \Cake\Http\Session $session */
        $session = $request->getAttribute('session');
        $token = $session->read("{$sessionKey}.accessToken");
        if ($token) {
            // 外部 API の認証も解除しておく
            $this->callApi('deauth', 'delete', [], [
                'Authorization' => "Bearer ${token}",
            ]);
        }

        return parent::clearIdentity($request, $response);
    }
}
