<?php

namespace Keboola\S3Extractor;

use Symfony\Component\Config\Definition\Processor;

class Application
{
    /** @var array */
    private $config;

    /** @var array */
    private $parameters;

    public function __construct($config)
    {
        $this->config = $config;
        $this->parameters = (new Processor)->processConfiguration(
            new ConfigDefinition,
            [$this->config['parameters']]
        );
    }

    /**
     * Runs data extraction
     * @param $outputPath
     * @return bool
     * @throws \Exception
     */
    public function actionRun($outputPath)
    {
        $extractor = new Extractor($this->parameters);
        return $extractor->extract($outputPath);
    }
}
