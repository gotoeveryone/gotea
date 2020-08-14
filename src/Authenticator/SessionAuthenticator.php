<?php
declare(strict_types=1);

namespace Gotea\Authenticator;

use Authentication\Authenticator\ResultInterface;
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
