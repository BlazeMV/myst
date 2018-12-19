<?php

namespace Blaze\Myst\Api\Objects;

use Blaze\Myst\Api\ApiObject;
use Illuminate\Support\Collection;

/**
 * @method int getId()
 * @method int getMessageId()
 * @method User getFrom()
 * @method string getDate()
 * @method Chat getChat()
 * @method User getForwardFrom()
 * @method Chat getForwardFromChat()
 * @method Message getReplyToMessage()
 * @method string getText()
 * @method Collection getEntities()
 * @method Collection getNewChatMembers()
 * @method User getLeftChatMember()
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
            'reply_to_message' => Message::class,
            'left_chat_member' => User::class,
        ];
    }
    
    protected function multipleObjectRelations(): array
    {
        return [
            'entities' => Entity::class,
            'new_chat_members' => User::class,
        ];
    }
}
