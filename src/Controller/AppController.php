<?php

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Psr\Log\LogLevel;

/**
 * アプリの共通コントローラ
 * 
 * @author		Kazuki Kamizuru
 * @since		2015/07/26
 * 
 * @property \App\Controller\Component\LogComponent $Log
 * @property \App\Controller\Component\MyAuthComponent $MyAuth
 */
class AppController extends Controller
{
    // コネクション
    private $__conn = null;

    // ロールバックフラグ
    private $__isRollback = false;

    // 許可するアクション
    private $__allowActions = ["index", "detail", "login", "logout"];

    // リダイレクト先アクション
    protected $_redirectAction = "index";

    /**
     * 初期処理
     */
	public function initialize()
    {
        $this->loadComponent('Csrf');
        $this->loadComponent('Flash');
        $this->loadComponent('Log');
        $this->loadComponent('MyAuth', [
            'loginAction' => [
                'controller' => 'users',
                'action' => 'index'
            ],
            'loginRedirect' => [
                'controller' => 'menu',
                'action' => 'index'
            ],
            'logoutRedirect' => [
                'controller' => 'users',
                'action' => 'index'
            ]
        ]);
    }

	/**
	 * アクション遷移前処理
     * 
     * @param Event $event
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        // 許可したアクション以外でPOSTではないアクセスの場合、デフォルトアクションへ遷移させる
        if (!in_array($this->request->action, $this->__allowActions) && !$this->request->is('post')) {
            $this->setAction($this->_redirectAction);
            return;
        }

        // トランザクションを開始
        if (empty($this->__conn)) {
            $this->__conn = ConnectionManager::get('default');
        }
        $this->__conn->begin();
    }

    /**
     * ビュー描画前処理
     * 
     * @param Event $event
     */
    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);

        // コミットまたはロールバック
        if (!empty($this->__conn)) {
            if ($this->__isRollback) {
                $this->log(__("例外が発生したので、トランザクションをロールバックしました。"), LogLevel::ERROR);
                $this->__conn->rollback();
            } else {
                $this->log(__("トランザクションをコミットしました。"), LogLevel::DEBUG);
                $this->__conn->commit();
            }
        }

        // ユーザ名を表示
        if ($this->MyAuth->user()) {
            $this->set('username', $this->MyAuth->user('userName'));
            $this->set('admin', ($this->MyAuth->user('role') === '管理者'));
        }
    }

    /**
     * リダイレクト処理
     * 
     * @param type $url
     * @param type $status
     */
    public function redirect($url, $status = 302)
    {
        // トランザクションをコミットしておく
        if (!empty($this->__conn)) {
            $this->__conn->commit();
        }
        parent::redirect($url, $status);
    }

    /**
     * 指定したリクエスト以外でアクセスがあった場合、デフォルトアクションへ遷移させます。
     * 
     * @param string $method
     * @param string $action
     */
    protected function _checkAllowRequest(string $method, string $action)
    {
        if (!$this->request->is($method)) {
            $this->setAction($action);
        }
    }

    /**
     * ロールバックをマークします。
     */
    protected function _markToRollback()
    {
        $this->__isRollback = true;
    }

    /**
     * コネクションを取得します。
     * 
     * @return \Cake\Datasource\ConnectionInterface A connection object.
     */
    protected function _getConnection()
    {
        return $this->__conn;
    }

    /**
     * エラーメッセージを取得します。
     * 
     * @param array $errors
     * @return string エラーメッセージ
     */
    protected function _getErrorMessage(array $errors = [])
    {
        $message = [];
        foreach ($errors as $error) {
            foreach ($error as $val) {
                $message[] = $val;
            }
        }
        return '<li>'.implode('<br/><li>', $message);
    }

    /**
     * リクエストから値を取りだします。
     * 存在しなければセッションから値を取り出します。
     * 
     * @param type $name
     * @return type
     */
    protected function _getParam($name = null)
    {
        $session = $this->request->session();
        $request_param = $this->request->data($name);
        return $request_param !== null ? $request_param : $session->read($name);
    }

    /**
     * セッションとビューに値を格納します。
     * 
     * @param type $name
     * @param type $value
     * @return type
     */
    protected function _setParam($name = null, $value = null)
    {
        $this->request->session()->write($name, $value);
        return $this->set($name, $value);
    }

    /**
     * タイトルタグに表示する値を設定します。
     *
     * @param string $title
     */
    protected function _setTitle(string $title)
    {
        $this->set('cakeDescription', $title);
    }

    /**
     * ダイアログ表示を設定します。
     */
    protected function _setDialogMode()
    {
        $this->set('isDialog', true);
    }

    /**
     * GETアクセスを許可するアクションを追加します。
     *
     * @param Array $actions
     */
    protected function _addAllowGetActions(Array $actions)
    {
        $detaultActions = $this->__allowActions;
        $this->__allowActions = array_merge($detaultActions, $actions);
    }
}
