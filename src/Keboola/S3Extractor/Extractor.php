<?php
namespace Keboola\S3Extractor;

use Aws\S3\S3Client;
use Aws\S3\Transfer;

class Extractor
{
    /**
     * @var array
     */
    private $parameters;

    /**
     * Extractor constructor.
     *
     * @param $parameters
     */
    public function __construct($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Creates exports and runs extraction
     * @param $outputPath
     * @return bool
     * @throws \Exception
     */
    public function extract($outputPath)
    {
        $client = new S3Client([
            'region' => 'us-east-1',
            'version' => '2006-03-01',
            'credentials' => [
                'key' => $this->parameters['accessKeyId'],
                'secret' => $this->parameters['secretAccessKey'],
            ]
        ]);
        $region = $client->getBucketLocation(["Bucket" => $this->parameters["bucket"]])->get('LocationConstraint');
        $client = new S3Client([
            'region' => $region,
            'version' => '2006-03-01',
            'credentials' => [
                'key' => $this->parameters['accessKeyId'],
                'secret' => $this->parameters['secretAccessKey'],
            ]
        ]);

        // Remove initial forwardslash
        $key = $this->parameters['key'];
        if (substr($key, 0, 1) == '/') {
            $key = substr($key, 1);
        }

        // Destination file
        $dst = $outputPath . '/' . substr($key, strrpos($key, '/'));

        $client->getObject([
            'Bucket' => $this->parameters['bucket'],
            'Key' => $key,
            'SaveAs' => $dst
        ]);

        return true;
    }
}
