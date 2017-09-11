<?php

namespace SyntaxErro\YMock\Behavior\Extension;

use SyntaxErro\YMock\Behavior\AbstractExtension;

class ReturningArrayMap extends AbstractExtension
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return '_returning_map';
    }

    public function configure(array $configuration, $returningMethodName)
    {
        return $this->mock->method($returningMethodName)->willReturnMap($configuration[$this->getName()]);
    }
}