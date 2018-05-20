<?php
namespace Gotea\Mailer;

use Cake\Collection\Collection;
use Cake\Log\Log;
use Cake\Mailer\Mailer;
use Exception;

/**
 * ユーザへの通知メーラー
 */
class UserMailer extends Mailer
{
    /**
     * Mailer's name.
     *
     * @var string
     */
    static public $name = 'User';

    /**
     * 通知メール送信情報を設定
     *
     * @param string $subject 件名
     * @param \Cake\Collection\Collection $messages 本文を設定したコレクション
     * @return void
     */
    public function notification(string $subject, Collection $messages)
    {
        $this->setSubject($subject)
            ->set(['messages' => $messages]);

        Log::info('メールを送信します。');
    }

    /**
     * 異常通知メール送信情報を設定
     *
     * @param string $subject 件名
     * @param \Exception $exception 例外
     * @return void
     */
    public function error(string $subject, Exception $exception)
    {
        $this->setSubject($subject)
            ->set(['messages' => $exception->getMessage()]);

        Log::error('異常通知メールを送信します。');
    }
}
