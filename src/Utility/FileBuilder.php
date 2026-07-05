<?php
declare(strict_types=1);

namespace Gotea\Utility;

use Cake\Core\Configure;
use Cake\Log\Log;
use InvalidArgumentException;
use RuntimeException;
use SplFileObject;

/**
 * JSON の I/O を行うためのクラス
 */
class FileBuilder
{
    /**
     * @var list<string> $parentDirPath
     */
    private array $parentDirPath = [];

    /**
     * Constructor
     */
    private function __construct()
    {
    }

    /**
     * ビルダクラスを初期化する
     *
     * @return $this
     */
    public static function new()
    {
        return new FileBuilder();
    }

    /**
     * 操作対象のディレクトリを取得する
     *
     * @return string
     */
    public function getDir(): string
    {
        return $this->buildDirPath();
    }

    /**
     * 親ディレクトリを設定する
     *
     * @param string $dir 親ディレクトリ
     * @return $this
     */
    public function setParentDir(string $dir)
    {
        $this->validatePathSegment($dir, 'parentDir');
        $this->parentDirPath = [$dir];

        return $this;
    }

    /**
     * 親ディレクトリのパスを設定する
     *
     * @param string ...$segments 親ディレクトリのパス要素
     * @return $this
     */
    public function setParentDirPath(string ...$segments)
    {
        if (!$segments) {
            throw new InvalidArgumentException('親ディレクトリが指定されていません');
        }

        foreach ($segments as $segment) {
            $this->validatePathSegment($segment, 'parentDir');
        }

        $this->parentDirPath = $segments;

        return $this;
    }

    /**
     * ファイルの出力処理を行う
     *
     * @param string $filename ファイル名（ディレクトリセパレータが含まれていた場合はエラーになる）
     * @param mixed $data 出力するデータ
     * @return bool 出力に成功すれば true
     */
    public function output(string $filename, mixed $data): bool
    {
        try {
            $this->validatePathSegment($filename, 'filename');

            // フォルダ作成
            $dirpath = $this->prepareOutputDir();
            $filepath = $dirpath . DS . "{$filename}.json";

            // ファイル作成
            $file = new SplFileObject($filepath, 'w');
            if (!$file->fwrite(json_encode($data))) {
                Log::error("ファイル作成に失敗しました: {$filepath}");

                return false;
            }

            return true;
        } catch (InvalidArgumentException | RuntimeException $e) {
            Log::error($e->getMessage());

            return false;
        }
    }

    /**
     * 操作対象のディレクトリパスを生成する
     *
     * @return string
     */
    private function buildDirPath(): string
    {
        $dir = rtrim((string)Configure::read('App.jsonDir'), DS);

        return $this->parentDirPath ? $dir . DS . implode(DS, $this->parentDirPath) : $dir;
    }

    /**
     * パス要素として安全な値か検証する
     *
     * @param string $value 検証する値
     * @param string $name 値の名前
     * @return void
     */
    private function validatePathSegment(string $value, string $name): void
    {
        if (
            trim($value) === ''
            || str_contains($value, '..')
            || str_contains($value, '/')
            || str_contains($value, '\\')
        ) {
            throw new InvalidArgumentException("不正なパス要素です: {$name}");
        }
    }

    /**
     * 出力先ディレクトリを作成する
     *
     * @return string
     */
    private function prepareOutputDir(): string
    {
        $baseDir = rtrim((string)Configure::read('App.jsonDir'), DS) . DS;
        $realBaseDir = realpath($baseDir);
        if ($realBaseDir === false) {
            throw new RuntimeException("JSON 出力ディレクトリが存在しません: {$baseDir}");
        }

        $baseDir = rtrim($realBaseDir, DS) . DS;
        $dirpath = rtrim($realBaseDir, DS);
        foreach ($this->parentDirPath as $segment) {
            $dirpath .= DS . $segment;

            if (is_link($dirpath)) {
                throw new InvalidArgumentException("出力先にシンボリックリンクは指定できません: {$dirpath}");
            }
            if (file_exists($dirpath)) {
                if (!is_dir($dirpath)) {
                    throw new InvalidArgumentException("出力先にディレクトリ以外は指定できません: {$dirpath}");
                }
            } elseif (!mkdir($dirpath, 0755)) {
                throw new RuntimeException("ディレクトリ作成に失敗しました: {$dirpath}");
            }

            $realDirpath = realpath($dirpath);
            if ($realDirpath === false) {
                throw new RuntimeException("出力先ディレクトリが存在しません: {$dirpath}");
            }
            $realDirpath = rtrim($realDirpath, DS) . DS;

            if (!str_starts_with($realDirpath, $baseDir)) {
                throw new InvalidArgumentException("JSON 出力ディレクトリ外への出力はできません: {$dirpath}");
            }

            $dirpath = rtrim($realDirpath, DS);
        }

        return $dirpath;
    }
}
