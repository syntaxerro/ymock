<?php

namespace SyntaxErro\YMock\Behavior\Extension;

use SyntaxErro\YMock\Behavior\AbstractExtension;

class ReturningClassicMock extends AbstractExtension
{
    public function configure(array $configuration, $returningMethodName)
    {
        $mockCreator = $this->getMockSuiteCreator($configuration, $returningMethodName);

        return $this->mock->method($returningMethodName)->willReturn($mockCreator->getMocks()->first());
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'class';
    }
}