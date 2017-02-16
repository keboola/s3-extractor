<?php

namespace Keboola\S3ExtractorTest;

use Keboola\S3Extractor\ConfigDefinition;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class ConfigDefinitionTest extends TestCase
{
    public function testValidConfigWithCredentials()
    {
        $json = <<<JSON
{
    "parameters": {
        "accessKeyId": "a",
        "#secretAccessKey": "b",
        "bucket": "c",
        "key": "d"
    }
}
JSON;

        $config = (new JsonDecode(true))->decode($json, JsonEncoder::FORMAT);
        $processor = new Processor;
        $processedConfig = $processor->processConfiguration(new ConfigDefinition(), [$config['parameters']]);

        $this->assertInternalType('array', $processedConfig);
    }

    public function testValidConfigWithRegion()
    {
        $json = <<<JSON
{
    "parameters": {
        "region": "myregion",
        "bucket": "c",
        "key": "d"
    }
}
JSON;

        $config = (new JsonDecode(true))->decode($json, JsonEncoder::FORMAT);
        $processor = new Processor;
        $processedConfig = $processor->processConfiguration(new ConfigDefinition(), [$config['parameters']]);

        $this->assertInternalType('array', $processedConfig);
    }

    public function testInvalidConfig()
    {
        $this->expectException(InvalidConfigurationException::class);

        $json = <<<JSON
{
    "parameters": {
        "region": "myregion",
        "bucket": "c"
    }   
}
JSON;

        $config = (new JsonDecode(true))->decode($json, JsonEncoder::FORMAT);
        (new Processor())->processConfiguration(new ConfigDefinition, [$config['parameters']]);
    }
}
