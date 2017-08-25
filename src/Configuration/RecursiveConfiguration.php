<?php

namespace SyntaxErro\YMock\Configuration;

class RecursiveConfiguration implements ConfigurationInterface
{
    use Configurable;

    /**
     * RecursiveConfiguration constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }
}