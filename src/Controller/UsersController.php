<?php

namespace App\Controller;

use PDOException;
use App\Model\Entity\User;
use Cake\Event\Event;
use Cake\I18n\Time;
use Cake\Validation\Validator;
use Psr\Log\LogLevel;

/**
 * ログイン用コントローラ
 *
 * @author		Kazuki Kamizuru
 * @since		2015/07/26
 */
class UsersController extends AppController {

	/**
	 * アクション実行前処理
     * 
     * @param $event
	 */
    public function beforeFilter(Event $event)
    {
		// すでにログイン済みならリダイレクト
        if ($this->Auth->user()) {
			return $this->redirect($this->Auth->redirectUrl());
        }
        parent::beforeFilter($event);
    }

	/**
	 * 描画前処理
     * 
     * @param $event
	 */
    public function beforeRender(Event $event)
    {
        $this->_setTitle('ログイン');
        parent::beforeRender($event);
    }

    /**
     * 初期表示処理
     */
    public function index()
    {
        return $this->render('index');
    }

    /**
     * 初期表示処理
     */
    public function login()
    {
        if (!$this->request->is('post')) {
            return $this->index();
        }

        // 入力チェック
        $res = $this->__createValidator()->errors([
            'username' => $this->request->data('username'),
            'password' => $this->request->data('password')
        ]);
        if ($res) {
            $this->Flash->error($this->_getErrorMessage($res));
            return $this->index();
        }

        // ユーザを1件取得
        $user = $this->Users->find()->where([
            'account' => $this->request->data('username')
        ])->all()->first();

        // ユーザが取得出来なければログインエラー
        if (!$user || !password_verify($this->request->data('password'), $user->password)) {
            $this->log(__("ログイン失敗！"), LogLevel::WARNING);
            $this->Flash->error(__("ユーザまたはパスワードが異なります。"));
            return $this->index();
        }

        try {
            // 最終ログイン日時を更新してデータを保存
            $user->last_logged = Time::now();
            $this->Users->save($user);
        } catch (PDOException $e) {
            $this->log(__("最終ログイン日時更新エラー：{$e->getMessage()}"), LogLevel::ERROR);
            $this->_markToRollback();
            $this->Flash->error(__("データの更新エラーが発生しました。"));
            return $this->index();
        }

        // ユーザ情報を設定
        $this->__setUser($user);
        $this->log("ユーザ：{$user->name}がログインしました。", LogLevel::INFO);

        // リダイレクト
        return $this->redirect($this->Auth->redirectUrl());
    }

    /**
     * ログアウト
     * @return bool
     */
    public function logout()
    {
        return $this->redirect($this->Auth->logout());
    }

    /**
     * ユーザ情報をセッションに格納します。
     * 
     * @param User $user
     */
    private function __setUser(User $user)
    {
        // ログイン情報を設定
        $this->Auth->setUser([
            'userid' => $user->account,
            'username' => $user->name,
            'role' => $user->role,
            'created' => $user->created,
            'modified' => $user->modified
        ]);
    }

    /**
     * ログイン時のバリデーションを生成します。
     * 
     * @return Validator
     */
    private function __createValidator()
    {
        // 入力チェック
        $validator = new Validator();
        $validator
            ->notEmpty('username', 'ユーザIDは必須です。')
            ->notEmpty('password', 'パスワードは必須です。')
            ->add('username', [
                'length' => [
                    'rule' => ['maxLength', 10],
                    'message' => 'ユーザIDは10文字以下で入力してください。'
                ]
            ])
            ->add('password', [
                'length' => [
                    'rule' => ['maxLength', 10],
                    'message' => 'パスワードは10文字以下で入力してください。'
                ]
            ]
        );
        return $validator;
    }
}
