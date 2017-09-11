<?php

namespace SyntaxErro\YMock\Behavior;

use SyntaxErro\YMock\Behavior\Extension\ReturningClassicMock;
use SyntaxErro\YMock\Configuration\Extensions;
use SyntaxErro\YMock\Configuration\RecursiveConfiguration;
use SyntaxErro\YMock\Exception\BehaviorException;
use SyntaxErro\YMock\Utils\ArrayHelper;
use SyntaxErro\YMock\Utils\ClassFinder;
use SyntaxErro\YMock\Creator\MocksSuiteCreator;

class MockBehaviorConfigurator
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
     * @var array
     */
    private $preloadedExtensionsClasses = [];

    /**
     * @var bool
     */
    private $isRecursive = true;

    /**
     * ReturnedValuesConfigurator constructor.
     * @param \PHPUnit_Framework_TestCase $testCase
     * @param \PHPUnit_Framework_MockObject_MockObject $mock
     * @param bool $isRecursive
     */
    public function __construct(\PHPUnit_Framework_TestCase $testCase, \PHPUnit_Framework_MockObject_MockObject $mock, $isRecursive = true)
    {
        $this->mock = $mock;
        $this->testCase = $testCase;
        $this->isRecursive = $isRecursive;
        $this->preloadedExtensionsClasses = ClassFinder::getClassesInNamespace(Extensions::EXTENSIONS_NAMESPACE);
    }

    /**
     * @param array $methods
     *
     * @throws \SyntaxErro\YMock\Exception\InaccessibleCollectionElementException
     * @throws BehaviorException
     */
    public function configureBehavior(array $methods)
    {
        foreach($methods as $name => $configOrReturnedValue) {
            if(is_array($configOrReturnedValue) && ArrayHelper::isAssoc($configOrReturnedValue)) {
                $loadedExtension = $this->loadEnabledExtension($methods);
                if($loadedExtension === null) {
                    $recursiveMockBuilder = new MocksSuiteCreator($this->testCase);

                    $recursiveConfiguration = new RecursiveConfiguration($configOrReturnedValue);
                    $recursiveMockBuilder->setConfiguration($recursiveConfiguration);

                    $this->mock->method($name)->willReturn($recursiveMockBuilder->getMocks()->toArray());
                    continue;
                }

                $loadedExtension->configure($configOrReturnedValue, $name);
            } else {
                $this->mock->method($name)->willReturn($configOrReturnedValue);
            }
        }
    }

    /**
     * Load enabled extension per mock by given mock configuration
     *
     * @param array $methods
     * @return null|AbstractExtension
     * @throws BehaviorException
     */
    private function loadEnabledExtension(array $methods)
    {
        $loadedExtensions = [];
        foreach($methods as $name => $config) {
            if($extensionInstance = $this->isExtensionEnabled($config)) {
                $loadedExtensions[] = $extensionInstance;
            }
        }

        if(count($loadedExtensions) > 1) {
            throw new BehaviorException(
                sprintf('Mock "%s" cannot accept more than 1 behaviors! Enabled extensions: ', get_class($this->mock), implode(', ', $loadedExtensions))
            );
        }

        return $loadedExtensions ? $loadedExtensions[0] : null;
    }

    /**
     * Return extension instance if is enabled or null if disabled
     *
     * @param $config
     * @return null|$extensionInstance
     * @throws BehaviorException
     */
    private function isExtensionEnabled($config)
    {
        $enabled = null;

        foreach($this->preloadedExtensionsClasses as $preloadedExtensionsClass) {
            $extensionInstance = new $preloadedExtensionsClass($this->testCase, $this->mock);
            if(!$extensionInstance instanceof AbstractExtension) {
                throw new BehaviorException(
                    sprintf('Extension "%s" should be class %s', $preloadedExtensionsClass, AbstractExtension::class)
                );
            }

            if($extensionInstance->isEnabled($config) || ($extensionInstance instanceof ReturningClassicMock && !$this->isRecursive)) {
                $enabled = $extensionInstance;
            }
        }

        return $enabled;
    }
}