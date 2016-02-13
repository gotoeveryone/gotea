<?php

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\NetWork\Exception\ForbiddenException;
use Psr\Log\LogLevel;

/**
 * アプリの共通コントローラ
 */
class AppController extends Controller
{
    // コネクション
    var $conn = null;

    // ロールバックフラグ
    var $isRollback = false;

    //
	// /**
	//  * 初期処理
	//  */
    // public function initialize()
    // {
    //     parent::initialize();
	//
    //     $this->loadComponent('RequestHandler');
    //     $this->loadComponent('Flash');
	//
    //     $this->Session = $this->request->session();
	//
    //     $this->loadComponent('Auth', [
    //         'authenticate' => [
    //             'Form' => [ // 認証の種類を指定。Form,Basic,Digestが使える。デフォルトはForm
    //                 // 'userModel' => 'Users',
    //                 'fields' => [ // ユーザー名とパスワードに使うカラムの指定。省略した場合はusernameとpasswordになる
    //                     'username' => 'USER_ID', // ユーザー名のカラムを指定
    //                     'password' => 'PASSWORD' //パスワードに使うカラムを指定
    //                 ]
    //             ]
    //         ],
    //         // 'loginAction' => [
    //         //     'controller' => 'Login',
    //         //     'action' => 'index'
    //         // ],
    //         'loginRedirect' => [
    //             'controller' => 'Menu',
    //             'action' => 'index'
    //         ],
    //         // 'logoutRedirect' => [
    //         //     'controller' => 'Login',
    //         //     'action' => 'index'
    //         // ],
    //         'authError' => __('You need Logged In.')
    //     ]);
    // }
	//
	// public function isAuthorized($user) /* add */
    // {
    //     return false;
    // }
	//
	// /**
	//  * 各アクションの事前に実行する処理
	//  */
    // public function beforeRender(Event $event) {
	//
    //     // if ($this->Auth->user() !== null) {
    //     //     $this->set('username', $this->Auth->user('User.USER_NAME'));
    //     // }
	//
	// 	// 初期表示以外のアクションの場合、POSTされたデータが存在しなければエラー
	// 	// if ($this->action !== 'index' && !$this->request->data) {
	// 	// 	throw new ForbiddenException('このページへのアクセスは許可されていません。');
	// 	// }
	//
    //     $dialogFlag = $this->request->data('dialogFlag');
    //     if ($dialogFlag == true) {
    //         $this->set('dialogFlag', true);
    //     } else {
    //         $this->set('dialogFlag', false);
    //     }
    // }

    /**
     * 初期処理
     */
	public function initialize()
    {
        $this->loadComponent('Flash');
        $this->loadComponent('Auth', [
                // 'authorize' => ['Controller'],
	            // 'authenticate' => [
	            //     'Form' => [ // 認証の種類を指定。Form,Basic,Digestが使える。デフォルトはForm
	            //         // 'userModel' => 'Users',
	            //         'fields' => [ // ユーザー名とパスワードに使うカラムの指定。省略した場合はusernameとpasswordになる
	            //             'username' => 'email', // ユーザー名のカラムを指定
	            //             'password' => 'password' //パスワードに使うカラムを指定
	            //         ]
	            //     ]
	            // ],
                'loginAction' => [
                    'controller' => 'Users',
                    'action' => 'index'
                ],
                'loginRedirect' => [
                    'controller' => 'Menu',
                    'action' => 'index'
                ],
                'logoutRedirect' => [
                    'controller' => 'Users',
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
        if (empty($this->conn)) {
            $this->conn = ConnectionManager::get('default');
        }
        $this->conn->begin();
        if ($this->Auth->user() !== null) {
            $this->set('username', $this->Auth->user('username'));
        }

        // ダイアログ表示判定
        $dialogFlag = $this->request->data('dialogFlag');
        if (!empty($dialogFlag) && $dialogFlag === 'true') {
            $this->set('dialogFlag', true);
        } else {
            $this->set('dialogFlag', false);
        }
    }

	/**
	 * ビュー描画前処理
     * 
     * @param Event $event
	 */
    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);
        if ($this->isRollback) {
			$this->log('例外が発生したので、トランザクションをロールバックしました。', LogLevel::ERROR);
            $this->conn->rollback();
        } else {
//			$this->log('トランザクションをコミットしました。', LogLevel::DEBUG);
            $this->conn->commit();
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
        if (!empty($this->conn)) {
            $this->conn->commit();
        }
        parent::redirect($url, $status);
    }

    /**
	 * ビュー描画後処理
     * 
     * @param Event $event
	 */
    public function afterFilter(Event $event)
    {
        parent::afterFilter($event);
    }

    // public function isAuthorized($user) /* add */
    // {
    //     return false;
    // }

    /**
     * エラーメッセージを取得
     * 
     * @param array $errors
     * @return string エラーメッセージ
     */
    protected function _getErrorMessage(array $errors = [])
    {
        $message = [];
        foreach ($errors as $error) {
            foreach ($error as $key => $val) {
                array_push($message, $val);
            }
        }
//        $this->log('　表示する文言'.implode('<br/>', $message), LogLevel::INFO);
        return implode('<br/>', $message);
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
//        $this->log($session->read($name), LogLevel::INFO);
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
//        $this->log('書き込む値->'.$name.'に'.$value, LogLevel::INFO);
        $this->request->session()->write($name, $value);
        return $this->set($name, $value);
    }
}
