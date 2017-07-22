<?php
namespace App\View\Helper;

use Cake\View\Helper\FlashHelper;

/**
 * 独自にカスタマイズしたFlashヘルパー
 */
class MyFlashHelper extends FlashHelper
{
    private $_prefix = '<li>';
    private $_suffix = '</li>';

    /**
     * エラーメッセージ単位に配列形式で取得する。
     *
     * @param string|array $messages
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

        return $this->_prefix.implode($this->_suffix.$this->_prefix, $out).$this->_suffix;
    }
}
