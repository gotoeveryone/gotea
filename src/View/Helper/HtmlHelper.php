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
    public function scriptBlock($script, array $options = []): ?string
    {
        // 開始・終了タグを除去しておく
        $s = preg_replace('{<script([\s\S]*?)>([\s\S]*?)</script>}', '$2', $script);

        return parent::scriptBlock($s, $options);
    }

    /**
     * 共通JSファイルを読み出します。
     *
     * @param string $path パス
     * @return string|null String of `<script />` tags or null if block is specified in options
     *   or if $once is true and the file has been included before.
     */
    public function commonScript(string $path): ?string
    {
        return $this->script(env('ASSETS_URL') . $path);
    }

    /**
     * 共通JSファイルを読み出します。
     *
     * @param string $path パス
     * @return string|null CSS `<link />` or `<style />` tag, depending on the type of link.
     */
    public function commonCss(string $path): ?string
    {
        return $this->css(env('ASSETS_URL') . $path);
    }
}
