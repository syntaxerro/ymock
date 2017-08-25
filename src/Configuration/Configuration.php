<?php

namespace SyntaxErro\YMock\Configuration;

use Symfony\Component\Yaml\Parser;
use SyntaxErro\YMock\Exception\InvalidConfigException;

/**
 * Class Configuration
 * @package SyntaxErro\YMock
 */
class Configuration implements ConfigurationInterface
{
    use Configurable;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * Configuration constructor.
     * @param string $configPath
     */
    public function __construct($configPath)
    {
        $this->parser = new Parser();

        $this->validateConfigurationFile($configPath);
        $this->openAndParseConfigurationFile($configPath);
    }

    /**
     * @param string $configPath
     * @throws InvalidConfigException
     */
    private function validateConfigurationFile($configPath)
    {
        if(!file_exists($configPath)) {
            throw new InvalidConfigException(
                sprintf('Configuration file "%s" does not exists!', $configPath)
            );
        }

        if(!is_readable($configPath)) {
            throw new InvalidConfigException(
                sprintf('Configuration file "%s" is not readable!', $configPath)
            );
        }
    }

    /**
     * @param string $configPath
     * @throws \Throwable
     */
    private function openAndParseConfigurationFile($configPath)
    {
        $configContent = file_get_contents($configPath);
        $this->config = $this->parser->parse($configContent);
    }
}