<?php
declare(strict_types=1);

namespace Gotea\Controller;

use Cake\Controller\ErrorController as BaseErrorController;

/**
 * アプリの共通例外コントローラ
 *
 * @property \Cake\Controller\Component\FlashComponent $Flash
 */
class ErrorController extends BaseErrorController
{
    use JsonResponseTrait;

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Flash');
    }
}
