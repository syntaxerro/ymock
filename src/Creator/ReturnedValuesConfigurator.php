<?php

namespace SyntaxErro\YMock\Creator;

use SyntaxErro\YMock\Utils\ArrayHelper;
use SyntaxErro\YMock\MockCreator;
use SyntaxErro\YMock\Configuration\RecursiveConfiguration;

class ReturnedValuesConfigurator
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mock;

    /**
     * @var \PHPUnit_Framework_TestCase
     */
    private $testCase;

    /**
     * ReturnedValuesConfigurator constructor.
     * @param \PHPUnit_Framework_TestCase $testCase
     * @param \PHPUnit_Framework_MockObject_MockObject $mock
     */
    public function __construct(\PHPUnit_Framework_TestCase $testCase, \PHPUnit_Framework_MockObject_MockObject $mock)
    {
        $this->mock = $mock;
        $this->testCase = $testCase;
    }

    /**
     * @param array $methods
     *
     * @throws \SyntaxErro\YMock\Exception\InaccessibleCollectionElementException
     */
    public function configureReturnedValues(array $methods)
    {
        foreach($methods as $name => $configOrReturnedValue) {
            if(is_array($configOrReturnedValue) && ArrayHelper::isAssoc($configOrReturnedValue)) {
                $recursiveMockBuilder = new MockCreator($this->testCase);

                if (isset($configOrReturnedValue['class'])) {
                    $recursiveConfiguration = new RecursiveConfiguration([$name => $configOrReturnedValue]);
                    $recursiveMockBuilder->setConfiguration($recursiveConfiguration);

                    $this->mock->method($name)->willReturn($recursiveMockBuilder->getMocks()->first());
                } else {
                    $recursiveConfiguration = new RecursiveConfiguration($configOrReturnedValue);
                    $recursiveMockBuilder->setConfiguration($recursiveConfiguration);

                    $this->mock->method($name)->willReturn($recursiveMockBuilder->getMocks()->toArray());
                }
            }  else {
                $this->mock->method($name)->willReturn($configOrReturnedValue);
            }
        }
    }
}