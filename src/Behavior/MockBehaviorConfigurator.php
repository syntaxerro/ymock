<?php

namespace SyntaxErro\YMock\Behavior;

use SyntaxErro\YMock\Exception\BehaviorException;
use SyntaxErro\YMock\Utils\ArrayHelper;
use SyntaxErro\YMock\Utils\ClassFinder;

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

    const EXTENSIONS_NAMESPACE = 'SyntaxErro\\YMock\\Behavior\\Extension';

    /**
     * ReturnedValuesConfigurator constructor.
     * @param \PHPUnit_Framework_TestCase $testCase
     * @param \PHPUnit_Framework_MockObject_MockObject $mock
     */
    public function __construct(\PHPUnit_Framework_TestCase $testCase, \PHPUnit_Framework_MockObject_MockObject $mock)
    {
        $this->mock = $mock;
        $this->testCase = $testCase;
        $this->preloadedExtensionsClasses = ClassFinder::getClassesInNamespace(self::EXTENSIONS_NAMESPACE);
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
                    throw new BehaviorException(
                        sprintf('Cannot load behavior for mock "%s"', get_class($this->mock))
                    );
                }

                $loadedExtension->configure($configOrReturnedValue, $name);

            }  else {
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

            if($extensionInstance->isEnabled($config)) {
                $enabled = $extensionInstance;
            }
        }

        return $enabled;
    }
}