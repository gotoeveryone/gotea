<?php
declare(strict_types=1);

namespace Gotea\Client;

use Aws\Result;
use Aws\S3\S3Client as BaseClient;
use Cake\Core\Configure;
use SplFileObject;

/**
 * APIコールを行うためのトレイト
 */
class S3Client
{
    private BaseClient $s3;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $this->s3 = new BaseClient([
            'credentials' => [
                'key' => Configure::read('Aws.S3.accessKey'),
                'secret' => Configure::read('Aws.S3.secretKey'),
            ],
            'region' => Configure::read('Aws.S3.region'),
            'version' => 'latest',
        ]);
    }

    /**
     * ファイルアップロード
     *
     * @param \SplFileObject $srcFile
     * @param string $key
     * @param string $contentType
     * @return \Aws\Result
     */
    public function upload(SplFileObject $srcFile, string $key, string $contentType): Result
    {
        return $this->s3->putObject([
            'Bucket' => Configure::read('Aws.S3.bucket'),
            'Key' => $key,
            'SourceFile' => $srcFile->getPathname(),
            'ContentType' => $contentType,
        ]);
    }
}
