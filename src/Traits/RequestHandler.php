<?php

namespace Blaze\Myst\Traits;

use Blaze\Myst\Api\Requests\BaseRequest;

trait RequestHandler
{
    /**
     * @param BaseRequest $request
     * @param callable|null $async_function
     * @return \Blaze\Myst\Api\Response
     * @throws \Blaze\Myst\Exceptions\ConfigurationException
     * @throws \Blaze\Myst\Exceptions\RequestException
     */
    public function sendRequest(BaseRequest $request, callable $async_function = null)
    {
        $request->setBot($this);
        return $request->send($async_function);
    }
}