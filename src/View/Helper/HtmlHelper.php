<?php
declare(strict_types=1);

namespace Gotea\View\Helper;

use Cake\View\Helper\HtmlHelper as BaseHtmlHelper;

/**
 * 独自にカスタマイズしたHTMLヘルパー
 */
class HtmlHelper extends BaseHtmlHelper
{
    /**
     * インラインで記載しているScriptの出力処理。
     *
     * Scriptタグを自動挿入されてしまうとIDE補完が効かず、
     * 各ビューにてタグを書くと二重出力になるため、
     * Scriptタグがあれば取り除くよう対応
     * ビューでは以下のように利用（PHPタグは省略）
     *
     *   $this->MyHtml->scriptStart();
     *     // ここにJS
     *   $this->MyHtml->scriptEnd();
     *
     * @param string $script スクリプト文字列
     * @param array $options オプション
     * @return string|null 出力するスクリプト
     */
    public function scriptBlock(string $script, array $options = []): ?string
    {
        // 開始・終了タグを除去しておく
        $s = preg_replace('{<script([\s\S]*?)>([\s\S]*?)</script>}', '$2', $script);

        return parent::scriptBlock($s, $options);
    }
}
