<?php

namespace Blaze\Myst\Api\Objects;

use Blaze\Myst\Api\ApiObject;

/**
 * @method User getUser()
 * @method string getStatus()
*/
class ChatMember extends ApiObject
{
        protected function singleObjectRelations(): array
        {
            return [
                'user' => User::class,
            ];
        }
}
