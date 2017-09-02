<?php

namespace SyntaxErro\YMock;

use SyntaxErro\YMock\Configuration\ConfigurationInterface;
use SyntaxErro\YMock\Creator\MockPrototypeCreator;
use SyntaxErro\YMock\Creator\PrototypeCreatorArguments;

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

        $creator = new MockPrototypeCreator(
            PrototypeCreatorArguments::create($this->testCase, $configuration)
        );
        $creator->createMocksPrototypes($mocksSourceArray);

        $this->mocks->setMocks($mocksSourceArray);
        return $this;
    }


    /**
     * @return MocksCollection
     */
    public function getMocks()
    {
        return $this->mocks;
    }
}