<?php

namespace App\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;

/**
 * ユーザ情報
 */
class User extends AppEntity
{
    // ユーザID
    public function getUserId() {
        return $this->get('USER_ID');
    }
    public function setUserId($userId) {
        $this->set('USER_ID', $userId);
    }

    // パスワード
    public function getPassword() {
        return $this->get('PASSWORD');
    }
    public function setPassword($password) {
        $this->PASSWORD = $password;
        // $this->set('PASSWORD', (new DefaultPasswordHasher)->hash($password));
    }

    // ユーザ名
    public function getUserName() {
        return $this->USER_NAME;
    }

    // 最終ログイン日時
    public function getLastLoginDatetime() {
        return $this->LAST_LOGIN_DATETIME;
    }
    public function setLastLoginDatetime($lastLoginDatetime) {
        $this->LAST_LOGIN_DATETIME = $lastLoginDatetime;
    }
    // protected function _setPassword($password)
    // {
    //     return (new DefaultPasswordHasher)->hash($password);
    // }
}
