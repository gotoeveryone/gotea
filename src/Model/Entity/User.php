<?php

namespace App\Model\Entity;

/**
 * ユーザエンティティ
 */
class User extends AppEntity
{
    public function setPassword($password) {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }
}
