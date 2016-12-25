<?php
namespace App\View\Helper;

use Cake\View\Helper\HtmlHelper;

/**
 * 独自にカスタマイズしたHTMLヘルパー
 */
class MyHtmlHelper extends HtmlHelper
{
    /**
     * インラインで記載しているScriptの出力処理。
     * 
     * Scriptタグを自動挿入されてしまうとIDE補完が効かず、
     * 各ビューにてタグを書くと二十出力になるため、
     * Scriptタグがあれば取り除くよう対応
     * ビューでは以下のように利用（PHPタグは省略）
     * 
     *   $this->MyHtml->scriptStart();
     *     // ここにJS
     *   $this->MyHtml->scriptEnd();
     * 
     * @param string $script
     * @param array $options
     */
    public function scriptBlock($script, array $options = [])
    {
        // 開始・終了タグを除去しておく
        $s = preg_replace('{<script([\s\S]*?)>([\s\S]*?)</script>}', '$2', $script);
        parent::scriptBlock($s, $options);
    }
}
