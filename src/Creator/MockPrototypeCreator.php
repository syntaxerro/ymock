<?php

namespace SyntaxErro\YMock\Creator;

use SyntaxErro\YMock\Behavior\MockBehaviorConfigurator;
use SyntaxErro\YMock\Configuration\ConfigurationInterface;
use SyntaxErro\YMock\Exception\InvalidConfigException;
use SyntaxErro\YMock\Exception\ReadConfigException;

class MockPrototypeCreator
{
    /**
     * @var \PHPUnit_Framework_TestCase
     */
    private $testCase;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * MockPrototypeCreator constructor.
     * @param PrototypeCreatorArguments $arguments
     */
    public function __construct(PrototypeCreatorArguments $arguments)
    {
        $this->testCase = $arguments->testCase;
        $this->configuration = $arguments->config;
    }


    /**
     * @param array $sourceArray
     *
     * @throws ReadConfigException
     * @throws InvalidConfigException
     */
    public function createMocksPrototypes(&$sourceArray)
    {
        foreach($this->configuration->getMainConfigKeys() as $key) {
            $mockConfig = $this->configuration->getMainConfigKeyChildren($key);

            if(!isset($mockConfig['class'])) {
                continue;
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

                $returnedValuesConfigurator = new MockBehaviorConfigurator($this->testCase, $mock);
                $returnedValuesConfigurator->configureBehavior($mockConfig['methods']);
                $sourceArray[$key] = $mock;
                continue;
            }

            $sourceArray[$key] = $mockBuilder->getMock();
        }
    }
}