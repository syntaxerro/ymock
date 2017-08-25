<?php

namespace SyntaxErro\YMock\Configuration;

interface ConfigurationInterface
{
    /**
     * @return array
     */
    public function getConfig();

    /**
     * @return array
     */
    public function getMainConfigKeys();

    /**
     * @param $mainKey
     * @return array|mixed
     */
    public function getMainConfigKeyChildren($mainKey);
}