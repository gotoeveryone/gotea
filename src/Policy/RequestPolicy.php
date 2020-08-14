<?php
declare(strict_types=1);

namespace Gotea\Policy;

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
    public function canAccess($identity, ServerRequest $request)
    {
        return true;
    }
}
