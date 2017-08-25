<?php

namespace SyntaxErro\YMock\Configuration;

use SyntaxErro\YMock\Exception\ReadConfigException;

trait Configurable
{
    /**
     * @var array
     */
    private $config = [];

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return array
     */
    public function getMainConfigKeys()
    {
        return array_keys($this->config);
    }

    /**
     * @param $mainKey
     * @return mixed
     * @throws ReadConfigException
     */
    public function getMainConfigKeyChildren($mainKey)
    {
        if(!isset($this->config[$mainKey])) {
            throw new ReadConfigException(
                sprintf('Main configuration key "%s" does not exists!', $mainKey)
            );
        }

        return $this->config[$mainKey];
    }
}