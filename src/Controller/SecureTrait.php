<?php

namespace Gotea\Controller;

use Cake\Core\Configure;

/**
 * SSLへのリダイレクトを管理する。
 *
 * @property \Cake\Controller\Component\SecurityComponent $Security
 */
trait SecureTrait
{
    /**
     * SSLを強制する。
     *
     * @return void
     */
    public function forceSSL()
    {
        if (!Configure::read('debug', false)
            && env('CAKE_ENV', 'local') !== 'local') {
            // SecurityComponentを有効化
            $this->loadComponent('Security', [
                'blackHoleCallback' => 'redirectSecure',
                'validatePost' => false,
            ]);
            $this->Security->requireSecure();
        }
    }

    /**
     * HTTPSへリダイレクト
     *
     * @return \Cake\Http\Response|null
     */
    public function redirectSecure()
    {
        $url = 'https://' . env('SERVER_NAME') . $this->request->getRequestTarget();

        return $this->redirect($url);
    }
}
