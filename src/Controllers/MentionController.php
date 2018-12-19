<?php

namespace Blaze\Myst\Controllers;

abstract class MentionController extends EntitiesController
{
    public function isCaseSensitive(): bool
    {
        return false;   //forcing mentions to be case insensitive
    }
    
    
    public function prefix()
    {
        return '@';
    }
}
