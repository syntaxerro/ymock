<?php

namespace SyntaxErro\YMock\Utils;

class ArrayHelper
{
    /**
     * Check given array is associative
     *
     * @param array $arr
     * @return bool
     */
    static function isAssoc(array $arr)
    {
        if (!$arr) {
            return false;
        }

        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}