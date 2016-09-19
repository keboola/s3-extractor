<?php

require __DIR__ . '/../vendor/autoload.php';

use Keboola\S3Extractor\Application;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

$arguments = getopt('', ['data:']);
if (!isset($arguments['data'])) {
    echo 'Data folder not set.' . "\n";
    exit(2);
}

$configFile = $arguments['data'] . '/config.json';
if (!file_exists($configFile)) {
    echo 'Config file not found' . "\n";
    exit(2);
}

define('ROOT_PATH', __DIR__ . '/..');

try {
    $jsonDecode = new JsonDecode(true);
    $config = $jsonDecode->decode(
        file_get_contents($arguments['data'] . '/config.json'),
        JsonEncoder::FORMAT
    );
    $outputPath = $arguments['data'] . '/out/files';

    $application = new Application($config);
    $application->actionRun($outputPath);
    exit(0);
} catch (\Keboola\S3Extractor\Exception $e) {
    echo $e->getMessage();
    exit(1);
}
