<?php
declare(strict_types=1);

namespace Gotea\Controller;

use Cake\Event\EventInterface;
use Cake\Http\Response;
use Gotea\Form\LoginForm;

/**
 * ログイン用コントローラ
 *
 * @author      Kazuki Kamizuru
 * @since       2015/07/26
 */
class UsersController extends AppController
{
    /**
     * @inheritDoc
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        $this->Authentication->allowUnauthenticated(['index', 'login']);
    }

    /**
     * 初期表示処理
     *
     * @return \Cake\Http\Response|null
     */
    public function index(): ?Response
    {
        // すでにログイン済みならリダイレクト
        if ($this->Authentication->getIdentity()) {
            return $this->redirect($this->getLoginRedirectUrl());
        }

        // リダイレクトパラメータがある（ログアウトされている状態でログイン必須のページにアクセスがあった）場合はメッセージを表示
        if ($this->request->getQuery('redirect')) {
            $this->Flash->error(__('Login required.'));
        }

        $this->viewBuilder()->setLayout('login');

        return $this->set('form', new LoginForm())->render();
    }

    /**
     * ログイン処理
     *
     * @return \Cake\Http\Response|null
     */
    public function login(): ?Response
    {
        // ログイン
        $credentials = $this->getRequest()->getParsedBody();
        $form = new LoginForm();
        if (!$form->validate($credentials)) {
            return $this->setErrors(400, $form->getErrors())->setAction('index');
        }

        $result = $this->Authentication->getResult();

        // ログイン成功
        if ($result->isValid()) {
            // リダイレクト先をクエリパラメータに設定しておく
            $this->setRequest($this->getRequest()->withQueryParams([
                'redirect' => $this->getRequest()->getData('redirect'),
            ]));

            return $this->redirect($this->getLoginRedirectUrl());
        }

        // ログイン失敗
        return $this->setErrors(401, __('Authentication failed'))->setAction('index');
    }

    /**
     * ログアウト
     *
     * @return \Cake\Http\Response|null
     */
    public function logout(): ?Response
    {
        return $this->redirect($this->Authentication->logout());
    }
}
