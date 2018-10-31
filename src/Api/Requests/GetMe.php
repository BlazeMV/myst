<?php

namespace Blaze\Myst\Api\Requests;

use Blaze\Myst\Api\Objects\User;

class GetMe extends BaseRequest
{
    protected function responseObject()
    {
        return User::class;
    }
}