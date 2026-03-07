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
     * @var array<string, array<string, string>>|null
     */
    private ?array $viteAssetMap = null;

    /**
     * @inheritDoc
     */
    public function css(array|string $path, array $options = []): ?string
    {
        if (is_array($path)) {
            $path = array_map($this->resolveViteCssPath(...), $path);
        } else {
            $path = $this->resolveViteCssPath($path);
        }

        return parent::css($path, $options);
    }

    /**
     * @inheritDoc
     */
    public function script(array|string $url, array $options = []): ?string
    {
        if (is_array($url)) {
            $url = array_map($this->resolveViteScriptPath(...), $url);
        } else {
            $url = $this->resolveViteScriptPath($url);
        }

        return parent::script($url, $options);
    }

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

    /**
     * @param string $path アセットパス
     * @return string 解決後のCSSパス
     */
    private function resolveViteCssPath(string $path): string
    {
        return $this->resolveViteAssetPath($path, 'css');
    }

    /**
     * @param string $path アセットパス
     * @return string 解決後のJSパス
     */
    private function resolveViteScriptPath(string $path): string
    {
        return $this->resolveViteAssetPath($path, 'script');
    }

    /**
     * @param string $path アセットパス
     * @param string $type css|script
     * @return string 解決後のアセットパス
     */
    private function resolveViteAssetPath(string $path, string $type): string
    {
        if ($this->isExternalAsset($path)) {
            return $path;
        }

        $map = $this->getViteAssetMap();
        if ($map === []) {
            return $path;
        }

        $key = $this->normalizeAssetKey($path);
        if ($key === '') {
            return $path;
        }

        return $map[$type][$key] ?? $path;
    }

    /**
     * @param string $path アセットパス
     * @return bool 外部URLかどうか
     */
    private function isExternalAsset(string $path): bool
    {
        return preg_match('/^[a-z][a-z0-9+.-]*:/i', $path) === 1;
    }

    /**
     * @param string $path アセットパス
     * @return string 解決に利用するキー
     */
    private function normalizeAssetKey(string $path): string
    {
        $normalizedPath = parse_url($path, PHP_URL_PATH);
        if (!is_string($normalizedPath)) {
            return '';
        }

        $basename = basename($normalizedPath);
        if ($basename === '') {
            return '';
        }

        return pathinfo($basename, PATHINFO_FILENAME);
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function getViteAssetMap(): array
    {
        if ($this->viteAssetMap !== null) {
            return $this->viteAssetMap;
        }

        $manifest = $this->loadViteManifest();
        $map = [
            'css' => [],
            'script' => [],
        ];
        foreach ($manifest as $source => $entry) {
            if (!is_string($source) || !is_array($entry)) {
                continue;
            }

            $sourceKey = $this->normalizeAssetKey($source);
            if ($sourceKey === '') {
                continue;
            }

            $file = $entry['file'] ?? null;
            if (is_string($file) && $file !== '') {
                $resolvedFile = '/' . ltrim($file, '/');
                if (str_ends_with($file, '.js')) {
                    $map['script'][$sourceKey] = $resolvedFile;
                }
                if (str_ends_with($file, '.css')) {
                    $map['css'][$sourceKey] = $resolvedFile;
                }
            }

            $cssFiles = $entry['css'] ?? null;
            if (is_array($cssFiles) && isset($cssFiles[0]) && is_string($cssFiles[0])) {
                $map['css'][$sourceKey] = '/' . ltrim($cssFiles[0], '/');
            }
        }

        $this->viteAssetMap = $map;

        return $this->viteAssetMap;
    }

    /**
     * @return array<string, mixed>
     */
    private function loadViteManifest(): array
    {
        $manifestPath = WWW_ROOT . '.vite' . DS . 'manifest.json';
        if (!is_file($manifestPath)) {
            return [];
        }

        $raw = file_get_contents($manifestPath);
        if ($raw === false || $raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return [];
        }

        return $decoded;
    }
}
