<?php

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\NetWork\Exception\MethodNotAllowedException;
use Psr\Log\LogLevel;

/**
 * アプリの共通コントローラ
 */
class AppController extends Controller
{
    // コネクション
    private $conn = null;

    // ロールバックフラグ
    private $isRollback = false;

    // タイトル
    private $title = '';

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
        $this->loadComponent('Security', [
                'csrfCheck' => false,
                'validatePost' => false
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

        // 初期表示以外のアクションの場合、POSTされたデータが存在しなければエラー
        $target_actions = ['index', 'login', 'detail', 'clear', 'logout'];
        if (!in_array($this->request->action, $target_actions) && !$this->request->is('post')) {
        	throw new MethodNotAllowedException('不正なリクエストです。リクエストタイプ：'.$this->request->method());
        }

        // トランザクションを開始
        if (empty($this->conn)) {
            $this->conn = ConnectionManager::get('default');
        }
        $this->conn->begin();

        // ダイアログ表示判定
        $dialogFlag = $this->request->data('dialogFlag');
        if ($dialogFlag === 'true') {
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

        // コミットまたはロールバック
        if (!empty($this->conn)) {
            if ($this->isRollback) {
                $this->log('例外が発生したので、トランザクションをロールバックしました。', LogLevel::ERROR);
                $this->conn->rollback();
            } else {
                $this->log('トランザクションをコミットしました。', LogLevel::DEBUG);
                $this->conn->commit();
            }
        }

        // ユーザ名を表示
        if ($this->Auth->user()) {
            $this->set('username', $this->Auth->user('username'));
            $this->set('admin', $this->Auth->user('admin'));
        }

        // タイトルを設定
        $this->set('cakeDescription', $this->title);
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
     * エラーメッセージを取得
     * 
     * @param array $errors
     * @return string エラーメッセージ
     */
    protected function _getErrorMessage(array $errors = [])
    {
        $message = [];
        foreach ($errors as $error) {
            foreach ($error as $val) {
                array_push($message, $val);
            }
        }
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
        $this->request->session()->write($name, $value);
        return $this->set($name, $value);
    }

    /**
     * タイトルタグに表示する値を設定します。
     *
     * @param type $title
     */
    protected function _setTitle($title = null)
    {
        $this->title = $title;
    }
}
