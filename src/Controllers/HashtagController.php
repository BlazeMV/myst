<?php

namespace Blaze\Myst\Controllers;

abstract class HashtagController extends EntitiesController
{
    public function isCaseSensitive(): bool
    {
        return false;   //forcing hashtags to be case insensitive
    }
}