<?php

namespace SyntaxErro\YMock\TestsUtils;

use SyntaxErro\YMock\Creator\MocksSuiteCreator;
use SyntaxErro\YMock\Configuration\Configuration;

trait TestUtils
{
    public static function configureMockCreatorWithConfigurationPath(\PHPUnit_Framework_TestCase $testCase, $configurationPath)
    {
        $mockCreator = new MocksSuiteCreator($testCase);

        $configuration = new Configuration($configurationPath);
        $mockCreator->setConfiguration($configuration);

        return $mockCreator->getMocks();
    }
}