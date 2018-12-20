<?php

namespace Blaze\Myst\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use \Blaze\Myst\Api\Requests\BaseRequest;

/**
 * @method static \Blaze\Myst\Api\Objects\Update handleUpdate(callable $pre_function = null)
 * @method static \Blaze\Myst\Api\Objects\Update getWebhookUpdate()
 * @method static \Blaze\Myst\Api\Response sendRequest(BaseRequest $request, callable $async_function = null)
*/
class Bot extends Facade
{
    /**
     * Get the registered name of the component.
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \Blaze\Myst\Bot::class;
    }
}
