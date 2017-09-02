<?php

namespace SyntaxErro\YMock\Creator;

use SyntaxErro\YMock\Configuration\ConfigurationInterface;

class PrototypeCreatorArguments
{
    /**
     * @var \PHPUnit_Framework_TestCase
     */
    public $testCase;

    /**
     * @var ConfigurationInterface
     */
    public $config;

    /**
     * @param \PHPUnit_Framework_TestCase $testCase
     * @param ConfigurationInterface $configuration
     *
     * @return PrototypeCreatorArguments
     */
    static function create(\PHPUnit_Framework_TestCase $testCase, ConfigurationInterface $configuration)
    {
        $selfClassName = self::class;
        $instance = new $selfClassName();

        $instance->testCase = $testCase;
        $instance->config = $configuration;

        return $instance;
    }
}