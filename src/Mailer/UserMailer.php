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
     * @param string|array $body
     * @return void
     */
    public function notification(string $subject, $messages)
    {
        // メッセージ生成
        if (!is_array($messages)) {
            $body = [$messages];
        } else {
            $body = [];
            foreach ($messages as $key => $values) {
                if (!$values) {
                    continue;
                }

                if (!is_array($values) && !$values instanceof \Traversable) {
                    $body[] = $values;
                    continue;
                }

                $body[] = "【${key}】";
                foreach ($values as $value) {
                    $body[] = $value;
                }
                $body[] = '';
            }
        }

        // 宛先
        $to = env('EMAIL_TO');
        if (strpos($to, ',') !== false) {
            $to = explode(',', $to);
        }

        // メール送信
        $this->addTo($to)
            ->setSubject($subject)
            ->set(['content' => implode("\n", $body)])
            ->setTemplate('default');

        Log::info('メールを送信します。');
    }
}
