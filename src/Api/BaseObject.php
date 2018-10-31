<?php

namespace Blaze\Myst\Api;

use Illuminate\Support\Collection;

abstract class BaseObject extends Collection
{
	/**
	 * @var array $errors
	 */
	protected $errors = [];
	
	/**
	 * returns whether or not this object has an error
	 * @return bool
	 */
	public function hasErrors()
	{
		return count($this->errors) !== 0;
	}
	
	/**
	 * returns an array of errors in the object
	 * @return array|bool
	 */
	public function getErrors()
	{
		if (!$this->hasErrors()) return false;
		
		return $this->errors;
	}
	
	/**
	 * alias for hasErrors()
	 * @return bool
	 */
	public function hasError()
	{
		return count($this->errors) !== 0;
	}
	
	/**
	 * returns the first error in the object
	 * @return array|bool
	 */
	public function getError()
	{
		if (!$this->hasErrors()) return false;
		
		return collect($this->errors)->first();
	}
	
	public function __call($method, $args)
    {
        if(method_exists($this,$method)) {
            return call_user_func_array($this->$method, $args);
        } else {
            if (starts_with($method,'get')){
                $key = str_after($method, 'get');
                if ($this->has(snake_case($key))) {
                    return $this->get(snake_case($key));
                }
            }
            trigger_error("Call to undefined method '{$method}'");
        }
    }
}