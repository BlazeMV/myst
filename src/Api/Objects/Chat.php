<?php

namespace Blaze\Myst\Api\Objects;

use Blaze\Myst\Api\ApiObject;

/**
 * @method int getId()
 * @method int getChatId()
 * @method string getType()
 * @method string getTitle()
 * @method string getUsername()
 * @method string getFirstName()
 * @method string getLastName()
 * @method string getDescription()
 * @method string getAllMembersAreAdministrators()
*/
class Chat extends ApiObject
{
        protected function singleObjectRelations(): array
        {
            return [
                'photo' => ChatPhoto::class,
            ];
        }
}