<?php
namespace Keboola\S3ExtractorTest;

use Keboola\S3Extractor\Extractor;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class OneFileTest extends TestCase
{
    const AWS_S3_BUCKET_ENV = 'AWS_S3_BUCKET';
    const AWS_S3_ACCESS_KEY_ENV = 'TESTS_AWS_ACCESS_KEY';
    const AWS_S3_SECRET_KEY_ENV = 'TESTS_AWS_SECRET_KEY';
    const AWS_REGION_ENV = 'AWS_REGION';

    protected $path = '/tmp/one-file';

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

    /**
     * @param $initialForwardSlash
     * @param $publicObject
     * @dataProvider optionsProvider
     */
    public function testSuccessfulDownloadFromRoot($initialForwardSlash, $publicObject)
    {
        $key = "file1.csv";
        if ($publicObject) {
            $key = "public/" . $key;
        }
        if ($initialForwardSlash) {
            $key = "/" . $key;
        }

        $testHandler = new TestHandler();

        $options = [
            "bucket" => getenv(self::AWS_S3_BUCKET_ENV),
            "key" => $key
        ];
        if (!$publicObject) {
            $options["accessKeyId"] = getenv(self::AWS_S3_ACCESS_KEY_ENV);
            $options["#secretAccessKey"] = getenv(self::AWS_S3_SECRET_KEY_ENV);
        } else {
            $options["region"] = getenv(self::AWS_REGION_ENV);
        }
        $extractor = new Extractor($options, (new Logger('test'))->pushHandler($testHandler));
        $extractor->extract($this->path);

        $expectedFile = $this->path . '/' . 'file1.csv';
        $this->assertFileExists($expectedFile);
        $this->assertFileEquals(__DIR__ . "/../../_data/file1.csv", $expectedFile);
        if ($publicObject) {
            $this->assertTrue($testHandler->hasInfo("Downloading file /public/file1.csv"));
        } else {
            $this->assertTrue($testHandler->hasInfo("Downloading file /file1.csv"));
        }
        $this->assertCount(1, $testHandler->getRecords());
    }

    /**
     * @param $initialForwardSlash
     * @param $publicObject
     * @dataProvider optionsProvider
     */
    public function testSuccessfulDownloadFromFolder($initialForwardSlash, $publicObject)
    {
        $key = "folder1/file1.csv";
        if ($publicObject) {
            $key = "public/" . $key;
        }
        if ($initialForwardSlash) {
            $key = "/" . $key;
        }
        $testHandler = new TestHandler();

        $options = [
            "bucket" => getenv(self::AWS_S3_BUCKET_ENV),
            "key" => $key
        ];
        if (!$publicObject) {
            $options["accessKeyId"] = getenv(self::AWS_S3_ACCESS_KEY_ENV);
            $options["#secretAccessKey"] = getenv(self::AWS_S3_SECRET_KEY_ENV);
        } else {
            $options["region"] = getenv(self::AWS_REGION_ENV);
        }

        $extractor = new Extractor($options, (new Logger('test'))->pushHandler($testHandler));
        $extractor->extract($this->path);

        $expectedFile = $this->path . '/' . 'file1.csv';
        $this->assertFileExists($expectedFile);
        $this->assertFileEquals(__DIR__ . "/../../_data/folder1/file1.csv", $expectedFile);
        if ($publicObject) {
            $this->assertTrue($testHandler->hasInfo("Downloading file /public/folder1/file1.csv"));
        } else {
            $this->assertTrue($testHandler->hasInfo("Downloading file /folder1/file1.csv"));
        }
        $this->assertCount(1, $testHandler->getRecords());
    }

    /**
     * @return array
     */
    public function optionsProvider()
    {
        return [
            [true, false],
            [false, false],
            [true, true],
            [false, true]
        ];
    }
}
