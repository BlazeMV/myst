<?php

namespace Blaze\Myst\Helpers;

class Arr
{
    public static function isValueTrue(array $array, $key)
    {
        $value = array_get($array, $key);
        
        return $value === null ? false : $value == true;
    }
}
