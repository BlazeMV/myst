<?php

namespace Blaze\Myst\Api\Objects;

use Blaze\Myst\Api\ApiObject;

/**
 * @method int getType()
 * @method int getOffset()
 * @method int getLength()
 * @method int getUrl()
 * @method int getUser()
 */
class Entity extends ApiObject
{
    protected function singleObjectRelations(): array
    {
        return [
            'user' => User::class
        ];
    }
    
    public function getText($text)
    {
        return substr($text, $this->getOffset(), $this->getLength());
    }
}