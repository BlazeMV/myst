<?php

namespace Blaze\Myst\Services;

use Blaze\Myst\Exceptions\ControllerExistsException;
use Blaze\Myst\Exceptions\StubException;
use Illuminate\Support\Facades\File;

class StubService
{
    /**
     * @param $type
     * @param $name
     * @param $path
     * @throws \ReflectionException
     * @throws ControllerExistsException
     * @throws StubException
     */
    public function makeStub($type, $name, $path)
    {
        try {
            $contents = File::get(ConfigService::getPackageAbsolutePath() ."Laravel/Commands/stubs/$type.stub");
    
            $contents = str_replace(['{name}', '{classname}'], [strtolower($name), $name], $contents);
    
            if (!File::exists($path))
                mkdir($path, 0755, true);
    
            if (File::exists("$path$name.php"))
                throw new ControllerExistsException("$name already exists at $path");
    
            File::put("$path$name.php", $contents);
        } catch (\Exception $exception) {
            if ($exception instanceof ControllerExistsException) throw new ControllerExistsException($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
            if ($exception instanceof \ReflectionException) throw new \ReflectionException($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
            throw new StubException($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }
    }
}