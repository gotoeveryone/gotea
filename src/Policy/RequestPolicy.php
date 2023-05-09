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
        $action = $request->getParam('action');

        // 管理者は許可
        if ($identity->get('is_admin')) {
            return true;
        }

        // 以下は管理者以外
        // 特定のコントローラはアクセスさせない
        if (in_array($controller, ['Notifications', 'TableTemplates'])) {
            return false;
        }

        // 特定のコントローラの特定のアクションはアクセスさせない
        if (in_array($controller, ['TitleScores']) && in_array($action, ['upload', 'update', 'delete', 'switchDivision'])) {
            return false;
        }

        // GETのみ受け付ける
        return $request->getMethod() === 'GET';
    }
}
