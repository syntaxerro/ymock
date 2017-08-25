<?php

namespace SyntaxErro\YMock\TestsUtils;

class FakeServiceContainer
{
    /**
     * @var array
     */
    private $services = [];

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * FakeServiceContainer constructor.
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return array
     */
    public function getServices()
    {
        return $this->services;
    }
}