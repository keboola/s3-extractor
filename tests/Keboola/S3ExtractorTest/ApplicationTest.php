<?php
namespace Keboola\S3ExtractorTest;

use Keboola\S3Extractor\Application;
use Keboola\S3Extractor\Exception;
use Monolog\Handler\NullHandler;
use Monolog\Handler\TestHandler;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    const AWS_REGION_ENV = 'AWS_REGION';
    const AWS_S3_BUCKET_ENV = 'AWS_S3_BUCKET';
    const AWS_S3_ACCESS_KEY_ENV = 'TESTS_AWS_ACCESS_KEY';
    const AWS_S3_SECRET_KEY_ENV = 'TESTS_AWS_SECRET_KEY';
    protected $path = '/tmp/application';

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

    public function testApplicationPrivateFile()
    {
        $config = [
            "parameters" => [
                "accessKeyId" => getenv(self::AWS_S3_ACCESS_KEY_ENV),
                "#secretAccessKey" => getenv(self::AWS_S3_SECRET_KEY_ENV),
                "bucket" => getenv(self::AWS_S3_BUCKET_ENV),
                "key" => "/file1.csv"
            ]
        ];
        $testHandler = new TestHandler();
        $application = new Application($config, $testHandler);
        $application->actionRun($this->path);
        $this->assertTrue($testHandler->hasInfo("Downloading file /file1.csv"));
    }

    public function testApplicationPublicFile()
    {
        $config = [
            "parameters" => [
                "region" => getenv(self::AWS_REGION_ENV),
                "bucket" => getenv(self::AWS_S3_BUCKET_ENV),
                "key" => "/public/file1.csv"
            ]
        ];
        $testHandler = new TestHandler();
        $application = new Application($config, $testHandler);
        $application->actionRun($this->path);
        $this->assertTrue($testHandler->hasInfo("Downloading file /public/file1.csv"));
    }


    public function testInvalidConfigMissingAWSCredentials()
    {
        $this->expectException(Exception::class);
        $config = [
            "parameters" => [
                "bucket" => "myBucket",
                "key" => "/file1.csv"
            ]
        ];
        new Application($config);
    }
}
