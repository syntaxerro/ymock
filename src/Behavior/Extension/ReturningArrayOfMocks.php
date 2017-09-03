<?php

namespace SyntaxErro\YMock\Behavior\Extension;

use SyntaxErro\YMock\Behavior\AbstractExtension;
use SyntaxErro\YMock\Utils\ArrayHelper;
use SyntaxErro\YMock\Configuration\RecursiveConfiguration;
use SyntaxErro\YMock\MockCreator;

class ReturningArrayOfMocks extends AbstractExtension
{
    public function isEnabled(array $configuration)
    {
        return is_array($configuration) && ArrayHelper::isAssoc($configuration);
    }

    public function configure(array $configuration, $returningMethodName)
    {
        $recursiveMockBuilder = new MockCreator($this->testCase);

        $recursiveConfiguration = new RecursiveConfiguration($configuration);
        $recursiveMockBuilder->setConfiguration($recursiveConfiguration);

        $this->mock->method($returningMethodName)->willReturn($recursiveMockBuilder->getMocks()->toArray());
    }

}