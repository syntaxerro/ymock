<?php

namespace SyntaxErro\YMock\Behavior;

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
     * Check extension is enabled
     *
     * @param array $configuration
     * @return boolean
     */
    abstract public function isEnabled(array $configuration);

    /**
     * Configure mock behavior (method or expectations) with given configuration
     *
     * @param array $configuration
     * @param string $returningMethodName
     * @return
     */
    abstract public function configure(array $configuration, $returningMethodName);
}