<?php

namespace SyntaxErro\YMock\Behavior\Extension;

use SyntaxErro\YMock\Behavior\AbstractExtension;
use SyntaxErro\YMock\Configuration\RecursiveConfiguration;
use SyntaxErro\YMock\MockCreator;

class ReturningOneMock extends AbstractExtension
{
    public function isEnabled(array $configuration)
    {
        return isset($configuration['class']);
    }

    public function configure(array $configuration, $returningMethodName)
    {
        $recursiveMockBuilder = new MockCreator($this->testCase);

        $recursiveConfiguration = new RecursiveConfiguration([$returningMethodName => $configuration]);
        $recursiveMockBuilder->setConfiguration($recursiveConfiguration);

        $this->mock->method($returningMethodName)->willReturn($recursiveMockBuilder->getMocks()->first());
    }
}