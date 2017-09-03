<?php

namespace SyntaxErro\YMock;

use SyntaxErro\YMock\Configuration\Configuration;
use SyntaxErro\YMock\Creator\MocksSuiteCreator;
use SyntaxErro\YMock\Utils\MocksCollection;

class YMock
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
     * YMock constructor.
     * @param \PHPUnit_Framework_TestCase $testCase
     * @param string $configurationPath
     */
    public function __construct(\PHPUnit_Framework_TestCase $testCase, $configurationPath)
    {
        $configuration = new Configuration($configurationPath);
        $this->createMocks($configuration);

        $this->testCase = $testCase;
    }

    /**
     * @param Configuration $configuration
     */
    private function createMocks(Configuration $configuration)
    {
        $mockCreator = new MocksSuiteCreator($this->testCase);
        $mockCreator->setConfiguration($configuration);

        $this->mocks = $mockCreator->getMocks();
    }

    /**
     * @return MocksCollection
     */
    public function getMocks()
    {
        return $this->mocks;
    }
}