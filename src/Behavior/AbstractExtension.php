<?php

namespace SyntaxErro\YMock\Behavior;

use SyntaxErro\YMock\Creator\MocksSuiteCreator;
use SyntaxErro\YMock\Configuration\RecursiveConfiguration;
use SyntaxErro\YMock\Exception\BehaviorException;

abstract class AbstractExtension
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mock;

    /**
     * @var \PHPUnit_Framework_TestCase
     */
    protected $testCase;

    /**
     * ExtensionInterface constructor.
     * @param \PHPUnit_Framework_TestCase $testCase
     * @param \PHPUnit_Framework_MockObject_MockObject $mock
     */
    public function __construct(\PHPUnit_Framework_TestCase $testCase, \PHPUnit_Framework_MockObject_MockObject $mock)
    {
        $this->mock = $mock;
        $this->testCase = $testCase;
    }

    /**
     * Create, configure and return recursive MockSuiteCreator
     *
     * @param array $configuration
     * @param $method
     * @return MocksSuiteCreator
     */
    protected function getMockSuiteCreator(array $configuration, $method)
    {
        $recursiveMockBuilder = new MocksSuiteCreator($this->testCase);

        $recursiveConfiguration = new RecursiveConfiguration([$method => $configuration]);
        $recursiveMockBuilder->setConfiguration($recursiveConfiguration);

        return $recursiveMockBuilder;
    }

    /**
     * Check extension is enabled
     *
     * @param array $configuration
     * @return boolean
     *
     * @throws BehaviorException
     */
    public function isEnabled(array $configuration)
    {
        return isset($configuration[$this->getName()]);
    }

    abstract public function configure(array $configuration, $returningMethodName);


    /**
     * Return configuration key which enable extension
     *
     * @return string
     */
    abstract public function getName();
}