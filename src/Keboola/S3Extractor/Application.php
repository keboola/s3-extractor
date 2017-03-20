<?php

namespace Keboola\S3Extractor;

use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use Symfony\Component\Config\Definition\Processor;

class Application
{
    /** @var array */
    private $config;

    /** @var array */
    private $parameters;
    /**
     * @var Logger
     */
    private $logger;

    public function __construct($config, HandlerInterface $handler = null)
    {
        $this->config = $config;
        $parameters = (new Processor)->processConfiguration(
            new ConfigDefinition,
            [$this->config['parameters']]
        );
        if (!isset($parameters['region']) && !isset($parameters['accessKeyId']) && !isset($parameters['#secretAccessKey'])) {
            throw new Exception('For public files set the \'region\' parameter, for private files set both \'accessKeyId\' and \'#secretAccessKey\'.');
        }
        $this->parameters = $parameters;
        $logger = new Logger('Log');
        if ($handler) {
            $logger->pushHandler($handler);
        }
        $this->logger = $logger;
    }

    /**
     * Runs data extraction
     * @param $outputPath
     * @return bool
     * @throws \Exception
     */
    public function actionRun($outputPath)
    {
        $extractor = new Extractor($this->parameters, $this->logger);
        return $extractor->extract($outputPath);
    }
}
