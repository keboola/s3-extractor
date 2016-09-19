<?php
namespace Keboola\S3ExtractorTest;

use Keboola\S3Extractor\Extractor;

class WildcardKeyTest extends \PHPUnit_Framework_TestCase
{
    const AWS_S3_BUCKET_ENV = 'AWS_S3_BUCKET';
    const AWS_S3_ACCESS_KEY_ENV = 'TESTS_AWS_ACCESS_KEY';
    const AWS_S3_SECRET_KEY_ENV = 'TESTS_AWS_SECRET_KEY';

    protected $path = '/tmp/wildcard';

    public function setUp()
    {
        if (!file_exists($this->path)) {
            mkdir($this->path);
        }
    }

    public function tearDown()
    {
        passthru('rm -rf ' . $this->path);
    }

    public function testSuccessfulDownloadFromRoot()
    {
        $extractor = new Extractor([
            "accessKeyId" => getenv(self::AWS_S3_ACCESS_KEY_ENV),
            "secretAccessKey" => getenv(self::AWS_S3_SECRET_KEY_ENV),
            "bucket" => getenv(self::AWS_S3_BUCKET_ENV),
            "key" => "/f*"
        ]);
        $extractor->extract($this->path);

        $expectedFile = $this->path . '/file1.csv';
        $this->assertFileExists($expectedFile);
        $this->assertFileEquals(__DIR__ . "/../../_data/file1.csv", $expectedFile);

        $this->assertFileNotExists($this->path . '/file2.csv');
        $this->assertFileNotExists($this->path . '/folder1/file1.csv');
        $this->assertFileNotExists($this->path . '/folder2/file1.csv');
        $this->assertFileNotExists($this->path . '/folder2/file2.csv');
    }

    public function testSuccessfulDownloadFromFolder()
    {
        $extractor = new Extractor([
            "accessKeyId" => getenv(self::AWS_S3_ACCESS_KEY_ENV),
            "secretAccessKey" => getenv(self::AWS_S3_SECRET_KEY_ENV),
            "bucket" => getenv(self::AWS_S3_BUCKET_ENV),
            "key" => "/folder2/*"
        ]);
        $extractor->extract($this->path);

        $expectedFile = $this->path . '/file1.csv';
        $this->assertFileExists($expectedFile);
        $this->assertFileEquals(__DIR__ . "/../../_data/folder2/file1.csv", $expectedFile);

        $expectedFile = $this->path . '/file2.csv';
        $this->assertFileExists($expectedFile);
        $this->assertFileEquals(__DIR__ . "/../../_data/folder2/file2.csv", $expectedFile);
    }
}
