<?php

namespace Blaze\Myst\Traits;

use Illuminate\Support\Facades\File;

trait Stubs
{
    protected function getStub($name)
    {
        return File::get(__DIR__."/stubs/$name.stub");
    }
    
    protected function fillStub($stub, $name)
    {
        return str_ireplace(['DummyName', 'DummyClassName'], [strtolower($name), $name], $stub);
    }
    
    protected function makeFile($path, $name, $content)
    {
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
        File::put($path . $name . '.php', $content);
    }
}