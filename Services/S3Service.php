<?php
// require './vendor/autoload.php';
require __DIR__ . '/../vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class S3Service {

    private $s3;
    private $bucket;

    public function __construct() {
        $this->s3 = new S3Client([
            'region' => 'us-east-2',
            'version' => 'latest',
            'credentials' => [
                'key'    => $_ENV['AWS_KEY'],
                'secret' => $_ENV['AWS_SECRET'],
            ],
        ]);

        $this->bucket = 'renova360pro-storage-colombia2025';
    }

    public function uploadImage($file) {
        try {
            $fileName = time() . '-' . basename($file['name']);

            $result = $this->s3->putObject([
                'Bucket' => $this->bucket,
                'Key'    => $fileName,
                'SourceFile' => $file['tmp_name'],
                'ContentType' => $file['type']
            ]);

            return $result['ObjectURL'];

        } catch (AwsException $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
}
