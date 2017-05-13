<?php

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Network\Response;

/**
 * アプリの共通コントローラ
 * 
 * @author  Kazuki Kamizuru
 * @since   2015/07/26
 * 
 * @property \App\Controller\Component\LogComponent $Log
 * @property \App\Controller\Component\TransactionComponent $Transaction
 * @property \App\Controller\Component\MyAuthComponent $MyAuth
 */
class AppController extends Controller
{
    // 許可するアクション
    protected $_allowActions = ["index", "detail", "login", "logout"];

    // リダイレクト先アクション
    protected $_redirectAction = "index";

    /**
     * ダイアログ表示状態かどうか
     *
     * @var boolean
     */
    private $__dialog = false;

    /**
     * 初期処理
     */
	public function initialize()
    {
        $this->loadComponent('Csrf');
        $this->loadComponent('Flash');
        $this->loadComponent('Log');
        $this->loadComponent('Transaction');
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
        if (!in_array($this->request->action, $this->_allowActions) && !$this->request->isPost()
                && method_exists($this, $this->_redirectAction)) {
            $this->setAction($this->_redirectAction);
            return;
        }

        // トランザクションの開始
        $this->Transaction->begin();
    }

    /**
     * アクション遷移後処理
     * 
     * @param Event $event
     */
    public function afterFilter(Event $event)
    {
        parent::afterFilter($event);

        // トランザクションのコミットまたはロールバック
        $this->Transaction->commitOrRollback();
    }

    /**
     * ビュー描画前処理
     * 
     * @param Event $event
     */
    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);

        // ユーザ名を表示
        if ($this->MyAuth->user()) {
            $this->set('username', $this->MyAuth->user('userName'));
            $this->set('admin', ($this->MyAuth->user('role') === '管理者'));
        }
    }

    /**
     * リダイレクト前処理
     * 
     * @param Event $event
     * @param type $url
     * @param Response $response
     */
    public function beforeRedirect(Event $event, $url, Response $response)
    {
        // トランザクションのコミットまたはロールバック
        $this->Transaction->commitOrRollback();

        return parent::beforeRedirect($event, $url, $response);
    }

    /**
     * 遷移先のアクションをセットします。
     * 加えて、初期表示するタブを制御します。
     * 
     * @param string $action
     * @param string $tabName
     * @param mixed $args
     * @return mixed Returns the return value of the called action
     */
    public function setTabAction(string $action, string $tabName, $args)
    {
        $this->set('tab', $tabName);
        return $this->setAction($action, $args);
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
     * タイトルタグに表示する値を設定します。
     *
     * @param string $title
     */
    protected function _setTitle(string $title)
    {
        $this->set('cakeDescription', $title);
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
     */
    protected function _setDialogMode()
    {
        $this->__dialog = true;
        $this->set('isDialog', true);
    }

    /**
     * GETアクセスを許可するアクションを追加します。
     *
     * @param Array $actions
     */
    protected function _addAllowGetActions(Array $actions)
    {
        $detaultActions = $this->_allowActions;
        $this->_allowActions = array_merge($detaultActions, $actions);
    }
}
