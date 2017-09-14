<?php
namespace App\Mailer;

use Cake\Log\Log;
use Cake\Mailer\Mailer;

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
     * @param string $subject
     * @param array $messages
     * @return void
     */
    public function notification(string $subject, array $messages)
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
}
