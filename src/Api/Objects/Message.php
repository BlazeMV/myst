<?php

namespace Blaze\Myst\Api\Objects;

use Blaze\Myst\Api\ApiObject;

/**
 * @method int getId()
 * @method int getMessageId()
 * @method User getFrom()
 * @method string getDate()
 * @method Chat getChat()
 * @method User getForwardFrom()
 * @method Chat getForwardFromChat()
 * @method string getReplyToMessage()
 * @method string getText()
 * @method Collection getEntities()
 */
class Message extends ApiObject
{
    protected function singleObjectRelations(): array
    {
        return [
            'from' => User::class,
            'chat' => Chat::class,
            'forward_from' => User::class,
            'forward_from_chat' => Chat::class,
        ];
    }
    
    protected function multipleObjectRelations(): array
    {
        return [
            'entities' => Entity::class
        ];
    }
}