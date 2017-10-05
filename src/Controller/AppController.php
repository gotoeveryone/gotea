<?php

namespace App\Controller;

use Cake\Controller\Controller;

/**
 * アプリの共通コントローラ
 *
 * @author  Kazuki Kamizuru
 * @since   2015/07/26
 *
 * @property \Cake\Controller\Component\FlashComponent $Flash
 * @property \Cake\Controller\Component\SecurityComponent $Security
 * @property \App\Controller\Component\MyAuthComponent $Auth
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
                'controller' => 'users',
                'action' => 'index',
            ],
            'loginRedirect' => [
                'controller' => 'players',
                'action' => 'index',
            ],
            'logoutRedirect' => [
                'controller' => 'users',
                'action' => 'index',
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
     * タイトルタグに表示する値を設定します。
     *
     * @param string $title
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
     * @param array|string $errors
     * @param string $title
     * @param string|null $view View to use for rendering
     * @param string|null $layout Layout to use
     * @return \Cake\Http\Response|null
     */
    protected function _renderWithErrors($errors, $title, $view = null, $layout = null)
    {
        return $this->_setErrors($errors)->_renderWith($title, $view, $layout);
    }

    /**
     * タイトルタグに表示する値を設定します。
     *
     * @param array $options
     * @param string|array $url A string or array-based URL pointing to another location within the app,
     *     or an absolute URL
     * @param int $status HTTP status code (eg: 301)
     * @return \Cake\Http\Response|null
     */
    protected function _redirectWith($options = [], string $url, $status = 302)
    {
        if ($options) {
            $this->set($options);
        }
        return $this->redirect($url, $status);
    }

    /**
     * セッションに書き込みます。
     *
     * @param string $key
     * @param mixed $value
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
     * @param string $key
     * @return mixed
     */
    protected function _consumeBySession(string $key)
    {
        return $this->request->session()->consume($key);
    }

    /**
     * エラーを設定します。
     *
     * @param array|string $errors
     * @return \Cake\Controller\Controller
     */
    protected function _setErrors($errors)
    {
        return $this->_setMessages($errors, 'error');
    }

    /**
     * エラーを設定します。
     *
     * @param array|string $messages
     * @param string $type
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
     * @param string $title
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
