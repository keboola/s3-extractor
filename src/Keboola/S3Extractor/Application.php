<?php

namespace Keboola\S3Extractor;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
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
        $logger = new Logger('Log');
        $streamHandler = new StreamHandler('php://stdout');
        $streamHandler->setFormatter(new LineFormatter("%message%"));
        $logger->pushHandler($streamHandler);
        $extractor = new Extractor($this->parameters, $logger);
        return $extractor->extract($outputPath);
    }
}
