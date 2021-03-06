<?php
declare(strict_types=1);

namespace Gotea\View\Helper;

use Cake\View\Helper\FlashHelper as BaseFlashHelper;

/**
 * 独自にカスタマイズしたFlashヘルパー
 */
class FlashHelper extends BaseFlashHelper
{
    /**
     * エラーメッセージ単位に配列形式で取得する。
     *
     * @param string|array $messages メッセージ一覧
     * @return array
     */
    public function getMessages($messages)
    {
        $out = [];
        if (is_string($messages)) {
            $out[] = $messages;
        } else {
            // バリデーションの場合、フィールド => [定義 => メッセージ]となっている
            foreach ($messages as $expr) {
                $out[] = array_shift($expr);
            }
        }

        return h(json_encode($out));
    }
}
