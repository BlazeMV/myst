<?php

namespace Blaze\Myst\Api\Objects;

use Blaze\Myst\Api\ApiObject;

/**
 * @method int getId()
 * @method User getFrom()
 * @method Message getMessage()
 * @method int getInlineMessageId()
 * @method string getChatInstance()
 * @method string getData()
 * @method string getGameShortName()
 */
class CallbackQuery extends ApiObject
{
    protected function singleObjectRelations(): array
    {
        return [
            'from' => User::class,
            'message' => Message::class,
        ];
    }
}