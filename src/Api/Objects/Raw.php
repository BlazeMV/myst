<?php

namespace Blaze\Myst\Api\Objects;

use Blaze\Myst\Api\BaseObject;

class Raw extends BaseObject
{
    public function __construct($data)
    {
        parent::__construct(['data' => $data]);
    }
}