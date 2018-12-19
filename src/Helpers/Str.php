<?php

namespace Blaze\Myst\Helpers;

class Str
{
    public static function compareCaseInsensitive(string $val1, string $val2)
    {
        return strtolower($val1) == strtolower($val2);
    }
}
