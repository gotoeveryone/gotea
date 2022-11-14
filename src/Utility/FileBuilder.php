<?php
declare(strict_types=1);

namespace Gotea\Utility;

use Cake\Core\Configure;
use Cake\Log\Log;
use RuntimeException;
use SplFileObject;

/**
 * JSON の I/O を行うためのクラス
 */
class FileBuilder
{
    /**
     * @var string $parentDir
     */
    private string $parentDir = null;

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
        return Configure::read('App.jsonDir') . DS . ($this->parentDir ? $this->parentDir . DS : '');
    }

    /**
     * 親ディレクトリを設定する
     *
     * @param string $dir 親ディレクトリ
     * @return $this
     */
    public function setParentDir(string $dir)
    {
        $this->parentDir = $dir;

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
            // フォルダ作成
            $dirpath = $this->getDir();
            if (!file_exists($dirpath) && !mkdir($dirpath, 0755, true)) {
                Log::error("ディレクトリ作成に失敗しました: {$dirpath}");

                return false;
            }

            // ファイル作成
            $filepath = $dirpath . DS . "{$filename}.json";
            $file = new SplFileObject($filepath, 'w');
            if (!$file->fwrite(json_encode($data))) {
                Log::error("ファイル作成に失敗しました: {$filepath}");

                return false;
            }

            return true;
        } catch (RuntimeException $e) {
            Log::error($e->getMessage());

            return false;
        }
    }
}
