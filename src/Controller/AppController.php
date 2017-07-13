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
        $this->loadComponent('Csrf');
        $this->loadComponent('Flash');
        $this->loadComponent('Log');
        $this->loadComponent('Transaction');
        $this->loadComponent('Auth', [
            'className' => 'MyAuth',
            'loginAction' => [
                'controller' => 'users',
                'action' => 'index'
            ],
            'loginRedirect' => [
                'controller' => 'players',
                'action' => 'index'
            ],
            'logoutRedirect' => [
                'controller' => 'users',
                'action' => 'index'
            ]
        ]);
    }

	/**
     * {@inheritDoc}
	 */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        // トランザクションの開始
        $this->Transaction->begin();
    }

	/**
     * {@inheritDoc}
	 */
    public function afterFilter(Event $event)
    {
        parent::afterFilter($event);

        // トランザクションのコミットまたはロールバック
        $this->Transaction->commitOrRollback();
    }

	/**
     * {@inheritDoc}
	 */
    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);

        // ユーザ名を表示
        if ($this->Auth->user()) {
            $this->set('username', $this->Auth->user('userName'));
            $this->set('admin', ($this->Auth->user('role') === '管理者'));
        }
    }

	/**
     * {@inheritDoc}
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
    public function setTabAction(string $action, string $tabName, ...$args)
    {
        return $this->set('tab', $tabName)->setAction($action, ...$args);
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
