<?php
namespace Gotea\Mailer;

use Cake\Collection\Collection;
use Cake\Log\Log;
use Cake\Mailer\Mailer;
use Exception;

/**
 * メーラー
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
     * 通知メール送信
     *
     * @param string $subject 件名
     * @param \Cake\Collection\Collection $messages 本文を設定したコレクション
     * @return void
     */
    public function notification(string $subject, Collection $messages)
    {
        // 宛先
        $to = env('EMAIL_TO');
        if (strpos($to, ',') !== false) {
            $to = explode(',', $to);
        }

        // メール送信
        $this->addTo($to)
            ->setSubject($subject)
            ->set(['messages' => $messages]);

        Log::info('メールを送信します。');
    }

    /**
     * 通知メール送信
     *
     * @param string $subject 件名
     * @param \Exception $exception 例外
     * @return void
     */
    public function error(string $subject, Exception $exception)
    {
        // 宛先
        $to = env('EMAIL_TO');
        if (strpos($to, ',') !== false) {
            $to = explode(',', $to);
        }

        // メール送信
        $this->addTo($to)
            ->setSubject($subject)
            ->set(['messages' => $exception->getMessage()]);

        Log::error('異常通知メールを送信します。');
    }
}
