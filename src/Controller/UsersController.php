<?php
declare(strict_types=1);

namespace Gotea\Controller;

use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\I18n\FrozenTime;
use Cake\Log\Log;
use Gotea\Form\LoginForm;

/**
 * ログイン用コントローラ
 *
 * @author      Kazuki Kamizuru
 * @since       2015/07/26
 * @property \Gotea\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
    /**
     * @inheritDoc
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        $this->Authentication->allowUnauthenticated(['index', 'login', 'logout']);
        $this->Authorization->skipAuthorization();
    }

    /**
     * 初期表示処理
     *
     * @return \Cake\Http\Response|null
     */
    public function index(): ?Response
    {
        $this->Authorization->skipAuthorization();
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
        $this->viewBuilder()->setLayout('login');

        // ログイン
        $credentials = $this->getRequest()->getParsedBody();
        $form = new LoginForm();
        $this->set('form', $form);

        if (!$form->validate($credentials)) {
            return $this->set('form', $form)
                ->setErrors(400, $form->getErrors())
                ->render('index');
        }

        $result = $this->Authentication->getResult();

        // ログイン成功
        if ($result->isValid()) {
            // 最終ログイン日時を更新
            /** @var \Gotea\Model\Entity\User $user */
            $user = $this->Users->patchEntity($result->getData(), ['last_logged' => FrozenTime::now()]);
            if (!$this->Users->save($user)) {
                // 仮に保存に失敗してもログ出力のみ行う
                Log::warning('Update failed: last_logged');
            }

            // リダイレクト先をクエリパラメータに設定しておく
            $this->setRequest($this->getRequest()->withQueryParams([
                'redirect' => $this->getRequest()->getData('redirect'),
            ]));

            return $this->redirect($this->getLoginRedirectUrl());
        }

        // ログイン失敗
        return $this->setErrors(401, __('Authentication failed'))->render('index');
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
