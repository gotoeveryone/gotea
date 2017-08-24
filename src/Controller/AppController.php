<?php

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;

/**
 * アプリの共通コントローラ
 *
 * @author  Kazuki Kamizuru
 * @since   2015/07/26
 *
 * @property \App\Controller\Component\LogComponent $Log
 * @property \App\Controller\Component\MyAuthComponent $Auth
 */
class AppController extends Controller
{
    /**
     * ダイアログ表示状態かどうか
     *
     * @var boolean
     */
    private $__dialog = false;

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        $this->loadComponent('Flash');
        $this->loadComponent('Log');
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
     * {@inheritDoc}
     */
    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);

        // 表示タブの指定があれば変数に設定
        if (($tab = $this->request->getQuery('tab'))) {
            $this->set('tab', $tab);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function isAuthorized($user = null)
    {
        // ユーザ名を表示
        if ($user) {
            $this->set('username', $user['userName']);
            $this->set('admin', ($user['role'] === '管理者'));
            return true;
        }

        // デフォルトは拒否
        return false;
    }

    /**
     * エラーを設定します。
     *
     * @param array|string $errors
     * @return Controller
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
     * @return Controller
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
     * @return Controller
     */
    protected function _setTitle(string $title)
    {
        return $this->set('cakeDescription', $title);
    }

    /**
     * ダイアログ表示状態かどうかを判定します。
     *
     * @return bool ダイアログ表示状態ならtrue
     */
    protected function _isDialogMode()
    {
        return $this->__dialog ?? false;
    }

    /**
     * ダイアログ表示を設定します。
     *
     * @return Controller
     */
    protected function _setDialogMode()
    {
        return $this->set('isDialog', ($this->__dialog = true));
    }
}
