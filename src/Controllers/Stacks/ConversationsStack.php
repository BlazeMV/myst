<?php

namespace Blaze\Myst\Controllers\Stacks;

use Blaze\Myst\Api\Objects\Update;
use Blaze\Myst\Bot;
use Blaze\Myst\Controllers\BaseController;
use Blaze\Myst\Controllers\ConversationController;
use Blaze\Myst\Exceptions\StackException;
use Blaze\Myst\Services\ConversationService;

class ConversationsStack extends BaseStack
{
    /**
     * @inheritdoc
     */
    public function addStackItem(BaseController $item): BaseController
    {
        if (!$item instanceof ConversationController) throw new StackException(get_class($item) . " must be an instance of " . ConversationController::class);
        if (array_has($this->items, $item->getName())) throw new StackException($item->getName() . " has already been registered as a conversation.");
        $this->items[$item->getName()] = $item;
        return $item;
    }
    
    /**
     * @inheritdoc
     * @throws \Blaze\Myst\Exceptions\ConfigurationException
     */
    public function processStack(Update $update)
    {
        $bot = $update->getBot();
        if (!$this->checkStackPrerequisites($bot, $update)) return false;
    
        $conversationService = new ConversationService();
        if (!$conversationService->hasConversation($update->getChat()->getId(), $update->getFrom()->getId())) return false;
    
        $convo = $conversationService->getConversation($this->getChat()->getId(), $this->getFrom()->getId());
        if ($update->getMessage()->getReplyToMessage()->getId() !== $convo['reply_message_id']) return false;
    
        $conversation = $this->getStackItem($convo['name']);
        if ($conversation === null) return false;

        $conversation->make($update);
    }
    
    /**
     * @inheritdoc
     * @throws \Blaze\Myst\Exceptions\ConfigurationException
     */
    protected function checkStackPrerequisites(Bot $bot, Update $update): bool
    {
        if ($bot->getConfig('process.conversations') == false)  return false;
        if ($update->detectType() !== 'message') return false;
        if ($update->getMessage()->getReplyToMessage() === null) return false;
        
        return true;
    }
    
    protected function checkItemPrerequisites(Bot $bot, Update $update, BaseController $item): bool
    {
        return true;
    }
}