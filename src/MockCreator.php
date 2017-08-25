<?php

namespace SyntaxErro\YMock;

use SyntaxErro\YMock\Configuration\ConfigurationInterface;
use SyntaxErro\YMock\Configuration\RecursiveConfiguration;
use SyntaxErro\YMock\Exception\InvalidConfigException;

class MockCreator
{
    /**
     * @var MocksCollection
     */
    private $mocks;

    /**
     * @var \PHPUnit_Framework_TestCase
     */
    private $testCase;

    /**
     * MockCreator constructor.
     * @param \PHPUnit_Framework_TestCase $testCase
     */
    public function __construct(\PHPUnit_Framework_TestCase $testCase)
    {
        $this->mocks = new MocksCollection();
        $this->testCase = $testCase;
    }

    /**
     * @param ConfigurationInterface $configuration
     * @return MockCreator
     */
    public function setConfiguration(ConfigurationInterface $configuration)
    {
        $mocksSourceArray = [];

        $this->createMocksPrototypes($configuration, $mocksSourceArray);

        $this->mocks->setMocks($mocksSourceArray);
        return $this;
    }


    /**
     * @param ConfigurationInterface $configuration
     * @param array $sourceArray
     * @throws Exception\ReadConfigException
     * @throws InvalidConfigException
     */
    private function createMocksPrototypes(ConfigurationInterface $configuration, &$sourceArray)
    {
        foreach($configuration->getMainConfigKeys() as $key) {
            $mockConfig = $configuration->getMainConfigKeyChildren($key);

            if(!isset($mockConfig['class'])) {
                return;
            }
            $mockBuilder = $this->testCase->getMockBuilder($mockConfig['class']);

            if(isset($mockConfig['disable_original_constructor']) && $mockConfig['disable_original_constructor']) {
                $mockBuilder->disableOriginalConstructor();
            }

            if(isset($mockConfig['disable_original_clone']) && $mockConfig['disable_original_clone']) {
                $mockBuilder->disableOriginalClone();
            }

            if(isset($mockConfig['disable_argument_cloning']) && $mockConfig['disable_argument_cloning']) {
                $mockBuilder->disableArgumentCloning();
            }

            if(isset($mockConfig['disable_proxying_to_original_methods']) && $mockConfig['disable_proxying_to_original_methods']) {
                $mockBuilder->disableProxyingToOriginalMethods();
            }


            if(isset($mockConfig['constructor_args'])) {
                if(!is_array($mockConfig['constructor_args'])) {
                    throw new InvalidConfigException(
                        sprintf('Mock "%s" constructor arguments must be an array!', $key)
                    );
                }

                if(isset($mockConfig['disable_original_constructor'])) {
                    throw new InvalidConfigException(
                        sprintf('Mock "%s" cannot set constructor argument with disabled original constructor!', $key)
                    );
                }

                $mockBuilder->setConstructorArgs($mockConfig['constructor_args']);
            }

            if(isset($mockConfig['proxy_target'])) {
                if(!is_object($mockConfig['proxy_target'])) {
                    throw new InvalidConfigException(
                        sprintf('Mock "%s" constructor arguments must be an object!', $key)
                    );
                }

                $mockBuilder->setProxyTarget($mockConfig['proxy_target']);
            }

            if(isset($mockConfig['methods'])) {
                if(!is_array($mockConfig['methods'])) {
                    throw new InvalidConfigException(
                        sprintf('Configured methods for mock "%s" must be an array!', $key)
                    );
                }

                $mockBuilder->setMethods(array_keys($mockConfig['methods']));
                $mock = $mockBuilder->getMock();

                $this->configureReturnedValues($mockConfig['methods'], $mock);
                $sourceArray[$key] = $mock;
                return;
            }

            $sourceArray[$key] = $mockBuilder->getMock();
        }
    }

    /**
     * Recursive configure returned values of mock
     *
     * @param array $methods
     * @param \PHPUnit_Framework_MockObject_MockObject $mock
     * @throws Exception\InaccessibleCollectionElementException
     */
    private function configureReturnedValues(array $methods, \PHPUnit_Framework_MockObject_MockObject $mock)
    {
        $i=0;

        foreach($methods as $name => $configOrReturnedValue) {
            if(is_array($configOrReturnedValue)) {
                $recursiveMockBuilder = new MockCreator($this->testCase);

                if (isset($configOrReturnedValue['class']) && $i++ > 0) {
                    $recursiveConfiguration = new RecursiveConfiguration($configOrReturnedValue);
                    $recursiveMockBuilder->setConfiguration($recursiveConfiguration);

                    $mock->method($name)->willReturn($recursiveMockBuilder->getMocks()->first());
                } else {
                    $recursiveConfiguration = new RecursiveConfiguration($configOrReturnedValue);
                    $recursiveMockBuilder->setConfiguration($recursiveConfiguration);

                    $mock->method($name)->willReturn($recursiveMockBuilder->getMocks()->toArray());
                }
            }  else {
                $mock->method($name)->willReturn($configOrReturnedValue);
            }

            $this->mocks->addMock($mock, $name);
        }
    }

    /**
     * @return MocksCollection
     */
    public function getMocks()
    {
        return $this->mocks;
    }
}