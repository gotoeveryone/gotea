<?php
declare(strict_types=1);

namespace Gotea\Policy;

use Authorization\IdentityInterface;
use Authorization\Policy\RequestPolicyInterface;
use Cake\Http\ServerRequest;

/**
 * 認可のポリシークラス
 */
class RequestPolicy implements RequestPolicyInterface
{
    /**
     * @inheritDoc
     */
    public function canAccess(?IdentityInterface $identity, ServerRequest $request)
    {
        if (!$identity) {
            return false;
        }

        $controller = $request->getParam('controller');

        // 管理者は許可
        if ($identity->get('is_admin')) {
            return true;
        }

        // 以下は管理者以外
        // 特定のコントローラはアクセスさせない
        if (in_array($controller, ['Notifications', 'NativeQuery', 'TableTemplates'])) {
            return false;
        }

        // GETのみ受け付ける
        return $request->getMethod() === 'GET';
    }
}
