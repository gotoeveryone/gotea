<?php
declare(strict_types=1);

namespace Gotea\Controller;

use Cake\Controller\Controller;
use Cake\Event\EventManager;
use Gotea\Event\LoggedUser;

/**
 * アプリの共通コントローラ
 *
 * @author  Kazuki Kamizuru
 * @since   2015/07/26
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 * @property \Authorization\Controller\Component\AuthorizationComponent $Authorization
 * @property \Cake\Controller\Component\FlashComponent $Flash
 */
abstract class AppController extends Controller
{
    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Flash');
        $this->loadComponent('Authentication.Authentication', [
            'logoutRedirect' => [
                '_name' => 'top',
            ],
        ]);
        $this->loadComponent('Authorization.Authorization');

        // 操作ユーザ記録イベントを設定
        $user = $this->Authentication->getIdentity();
        if ($user) {
            // モデル側のインスタンスイベントより先に実行する必要があるため、グローバルイベントマネージャに登録する
            EventManager::instance()->on(new LoggedUser($user->getOriginalData()));
        }
    }

    /**
     * 指定ビューをレンダリングします。
     *
     * @param string $title タイトルに設定する文字列
     * @param string|null $view View to use for rendering
     * @param string|null $layout Layout to use
     * @return \Cake\Http\Response|null
     */
    protected function renderWith(string $title, $view = null, $layout = null)
    {
        return $this->setTitle($title)->render($view, $layout);
    }

    /**
     * 指定ビューをレンダリングします（ダイアログ表示）。
     *
     * @param string|null $view View to use for rendering
     * @param string|null $layout Layout to use
     * @return \Cake\Http\Response|null
     */
    protected function renderWithDialog($view = null, $layout = null)
    {
        return $this->enableDialogMode()->render($view, $layout);
    }

    /**
     * エラーを設定します。
     *
     * @param int $code ステータスコード
     * @param array|string $errors エラー
     * @param string $title タイトル
     * @param string|null $view View to use for rendering
     * @param string|null $layout Layout to use
     * @return \Cake\Http\Response|null
     */
    protected function renderWithErrors(int $code, $errors, $title, $view = null, $layout = null)
    {
        return $this->setErrors($code, $errors)->renderWith($title, $view, $layout);
    }

    /**
     * エラーを設定します（ダイアログ表示）。
     *
     * @param int $code ステータスコード
     * @param array|string $errors エラー
     * @param string|null $view View to use for rendering
     * @param string|null $layout Layout to use
     * @return \Cake\Http\Response|null
     */
    protected function renderWithDialogErrors(int $code, $errors, $view = null, $layout = null)
    {
        return $this->setErrors($code, $errors)->renderWithDialog($view, $layout);
    }

    /**
     * エラーを設定します。
     *
     * @param int $code ステータスコード
     * @param array|string $errors エラー
     * @return \Gotea\Controller\AppController
     */
    protected function setErrors(int $code, $errors)
    {
        $this->setResponse($this->getResponse()->withStatus($code));

        return $this->setMessages($errors, 'error');
    }

    /**
     * メッセージを設定します。
     *
     * @param array|string $messages メッセージ
     * @param string $type メッセージの種類
     * @return \Gotea\Controller\AppController
     */
    protected function setMessages($messages, $type = 'success')
    {
        $this->Flash->$type($messages);

        return $this;
    }

    /**
     * タイトルタグに表示する値を設定します。
     *
     * @param string $title タイトル
     * @return \Gotea\Controller\AppController
     */
    protected function setTitle(string $title)
    {
        return $this->set('pageTitle', $title);
    }

    /**
     * ダイアログ表示を設定します。
     *
     * @return \Gotea\Controller\AppController
     */
    protected function enableDialogMode()
    {
        return $this->set('isDialog', true);
    }

    /**
     * ログイン済みの場合にリダイレクトする URL を取得する
     *
     * @return string URL
     */
    protected function getLoginRedirectUrl()
    {
        return $this->Authentication->getLoginRedirect() ?? [
            '_name' => 'players',
        ];
    }
}
