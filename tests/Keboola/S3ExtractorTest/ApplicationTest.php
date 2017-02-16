<?php
namespace Keboola\S3ExtractorTest;

use Keboola\S3Extractor\Application;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
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

    public function testApplication()
    {
        $config = [
            "parameters" => [
                "accessKeyId" => getenv(self::AWS_S3_ACCESS_KEY_ENV),
                "#secretAccessKey" => getenv(self::AWS_S3_SECRET_KEY_ENV),
                "bucket" => getenv(self::AWS_S3_BUCKET_ENV),
                "key" => "/file1.csv"
            ]
        ];
        $application = new Application($config);
        $application->actionRun($this->path);
    }
}
