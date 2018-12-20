<?php

namespace Blaze\Myst\Api;

use Illuminate\Support\Collection;

abstract class BaseObject extends Collection
{
    public function __call($method, $args)
    {
        if (method_exists($this, $method)) {
            return call_user_func_array($this->$method, $args);
        } else {
            if (starts_with($method, 'get')) {
                $key = str_after($method, 'get');
                if ($this->has(snake_case($key))) {
                    return $this->get(snake_case($key));
                }
            }
//            trigger_error("Call to undefined method '{$method}'");
            return null;
        }
    }
}
