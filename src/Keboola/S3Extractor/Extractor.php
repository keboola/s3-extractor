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

        $filesToDownload = [];
        
        // Detect wildcard at the end
        if (substr($key, -1) == '*' || substr($key, - 1) == '%') {
            $iterator = $client->getIterator('ListObjects', [
                'Bucket' => $this->parameters['bucket'],
                'Prefix' => substr($key, 0, strlen($key) - 1)
            ]);
            foreach ($iterator as $object) {
                // Skip objects in Glacier
                if ($object['StorageClass'] === "GLACIER") {
                    continue;
                }
                // Skip objects in subfolders
                if (strrpos($object['Key'], '/', strlen($key)) !== false) {
                    continue;
                }
                $dst = $outputPath . '/' . substr($object['Key'], strrpos($object['Key'], '/'));
                $filesToDownload[] = [
                    'Bucket' => $this->parameters['bucket'],
                    'Key' => $object['Key'],
                    'SaveAs' => $dst
                ];
            }
        } else {
            $dst = $outputPath . '/' . substr($key, strrpos($key, '/'));
            $filesToDownload[] = [
                'Bucket' => $this->parameters['bucket'],
                'Key' => $key,
                'SaveAs' => $dst
            ];
        }

        foreach ($filesToDownload as $fileToDownload) {
            $client->getObject($fileToDownload);
        }
        return true;
    }
}
