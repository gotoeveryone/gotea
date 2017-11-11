<?php

namespace Gotea\Controller;

use Cake\Controller\Controller;

/**
 * アプリの共通コントローラ
 *
 * @author  Kazuki Kamizuru
 * @since   2015/07/26
 *
 * @property \Cake\Controller\Component\FlashComponent $Flash
 * @property \Cake\Controller\Component\SecurityComponent $Security
 * @property \Gotea\Controller\Component\MyAuthComponent $Auth
 */
class AppController extends Controller
{
    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        // ローカル以外はSSLを強制
        if (env('CAKE_ENV', 'local') !== 'local') {
            $this->loadComponent('Security', [
                'blackHoleCallback' => 'forceSSL',
                'validatePost' => false,
            ]);
            $this->Security->requireSecure();
        }

        $this->loadComponent('Flash');
        $this->loadComponent('Auth', [
            'className' => 'MyAuth',
            'loginAction' => [
                '_name' => 'top',
            ],
            'loginRedirect' => [
                '_name' => 'players',
            ],
            'logoutRedirect' => [
                '_name' => 'top',
            ],
            'authorize' => 'Controller',
        ]);
    }

    /**
     * SSLを強制する
     *
     * @return \Cake\Http\Response|null
     */
    public function forceSSL()
    {
        return $this->redirect('https://' . env('SERVER_NAME') . $this->request->here());
    }

    /**
     * {@inheritDoc}
     */
    public function isAuthorized($user = null)
    {
        if ($user) {
            $this->set([
                'userid' => $user['userId'],
                'username' => $user['userName'],
                'admin' => ($user['role'] === '管理者'),
            ]);

            return true;
        }

        // デフォルトは拒否
        return false;
    }

    /**
     * 指定ビューをレンダリングします。
     *
     * @param string $title タイトルに設定する文字列
     * @param string|null $view View to use for rendering
     * @param string|null $layout Layout to use
     * @return \Cake\Http\Response|null
     */
    protected function _renderWith(string $title, $view = null, $layout = null)
    {
        return $this->_setTitle($title)->render($view, $layout);
    }

    /**
     * タイトルタグに表示する値を設定します。
     *
     * @param string|null $view View to use for rendering
     * @param string|null $layout Layout to use
     * @return \Cake\Http\Response|null
     */
    protected function _renderWithDialog($view = null, $layout = null)
    {
        return $this->_enableDialogMode()->render($view, $layout);
    }

    /**
     * エラーを設定します。
     *
     * @param array|string $errors エラー
     * @param string $title タイトル
     * @param string|null $view View to use for rendering
     * @param string|null $layout Layout to use
     * @return \Cake\Http\Response|null
     */
    protected function _renderWithErrors($errors, $title, $view = null, $layout = null)
    {
        return $this->_setErrors($errors)->_renderWith($title, $view, $layout);
    }

    /**
     * 指定のURLへリダイレクトします。
     *
     * @param array $options オプション
     * @param string|array $url A string or array-based URL pointing to another location within the app,
     *     or an absolute URL
     * @param int $status HTTP status code (eg: 301)
     * @return \Cake\Http\Response|null
     */
    protected function _redirectWith($options, $url, $status = 302)
    {
        if ($options) {
            $this->set($options);
        }

        return $this->redirect($url, $status);
    }

    /**
     * セッションに書き込みます。
     *
     * @param string $key キー
     * @param mixed $value 値
     * @return \Cake\Controller\Controller
     */
    protected function _writeToSession($key, $value)
    {
        $this->request->session()->write($key, $value);

        return $this;
    }

    /**
     * 指定キーの値をセッションから取り出します。
     *
     * @param string $key キー
     * @return mixed
     */
    protected function _consumeBySession(string $key)
    {
        return $this->request->session()->consume($key);
    }

    /**
     * エラーを設定します。
     *
     * @param array|string $errors エラー
     * @return \Cake\Controller\Controller
     */
    protected function _setErrors($errors)
    {
        return $this->_setMessages($errors, 'error');
    }

    /**
     * メッセージを設定します。
     *
     * @param array|string $messages メッセージ
     * @param string $type メッセージの種類
     * @return \Cake\Controller\Controller
     */
    protected function _setMessages($messages, $type = 'info')
    {
        $this->Flash->$type($messages);

        return $this;
    }

    /**
     * タイトルタグに表示する値を設定します。
     *
     * @param string $title タイトル
     * @return \Cake\Controller\Controller
     */
    protected function _setTitle(string $title)
    {
        return $this->set('cakeDescription', $title);
    }

    /**
     * ダイアログ表示を設定します。
     *
     * @return \Cake\Controller\Controller
     */
    protected function _enableDialogMode()
    {
        return $this->set('isDialog', true);
    }
}
