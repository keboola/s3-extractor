<?php
/**
 * Loads test fixtures into S3
 */

date_default_timezone_set('Europe/Prague');
ini_set('display_errors', true);
error_reporting(E_ALL);

$basedir = dirname(__DIR__);

require_once $basedir . '/vendor/autoload.php';

$client =  new \Aws\S3\S3Client([
    'region' => getenv('AWS_REGION'),
    'version' => '2006-03-01',
    'credentials' => [
        'key' => getenv('PREPARE_TESTS_AWS_ACCESS_KEY'),
        'secret' => getenv('PREPARE_TESTS_AWS_SECRET_KEY'),
    ],
]);

// Where the files will be source from
$source = $basedir . '/tests/_data';

// Where the files will be transferred to
$bucket = getenv('AWS_S3_BUCKET');
$dest = 's3://' . $bucket;

// clear bucket
$result = $client->listObjects([
    'Bucket' => $bucket,
]);

$objects = $result->get('Contents');
if ($objects) {
    $client->deleteObjects([
        'Bucket' => $bucket,
        'Delete' => [
            'Objects' => array_map(function ($object) {
                return [
                    'Key' => $object['Key'],
                ];
            }, $objects),
        ],
    ]);
}

// Create a transfer object.
$manager = new \Aws\S3\Transfer($client, $source, $dest, [
    'debug' => true,
]);

// Perform the transfer synchronously.
$manager->transfer();

// duplicate into public folder
// Create a transfer object.
$manager = new \Aws\S3\Transfer($client, $source, $dest . '/public', [
    'debug' => true,
]);

// Perform the transfer synchronously.
$manager->transfer();


// put empty folder
print "Creating /emptyfolder/\n";
$client->putObject([
    'Bucket' => $bucket,
    'Key' => 'emptyfolder/'
]);

$client->putObject([
    'Bucket' => $bucket,
    'Key' => 'public/emptyfolder/'
]);

// setting ACL for public files
print "Setting ACL for public files\n";
// clear bucket
$result = $client->listObjects([
    'Bucket' => $bucket,
]);
$objects = $result->get('Contents');
if ($objects) {
    foreach ($objects as $object) {
        if (substr($object['Key'], 0, 7) == 'public/') {
            $client->putObjectAcl([
                'Bucket' => $bucket,
                'Key' => $object['Key'],
                'ACL' => 'public-read'
            ]);
        }
    }
}

echo "Data loaded OK\n";
