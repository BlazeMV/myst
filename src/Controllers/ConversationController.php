<?php

namespace Blaze\Myst\Controllers;

use Blaze\Myst\Api\Objects\Update;
use Blaze\Myst\Bot;
use Blaze\Myst\Services\ConversationService;
use Carbon\Carbon;

abstract class ConversationController extends BaseController
{
    /**
     * @var ConversationService $conversation_service
    */
    protected $conversation_service;
    
    public function make(Bot $bot, Update $update, array $conversation)
    {
        $this->setup($bot, $update);
        $this->conversation_service = new ConversationService();
    
        $this->handle($conversation['step']);
    }
    
    protected function getConversationService()
    {
        return $this->conversation_service;
    }
    
    public function getConversation()
    {
        return $this->getConversationService()->getConversation($this->getUpdate()->getChat()->getId(), $this->getUpdate()->getFrom()->getId());
    }
    
    public static function init(Update $update, $bot_message_id)
    {
        $convo = [
            'name' => (new static())->getName(),
            'step' => 2,
            'reply_message_id' => $bot_message_id,
            'expires_at' => Carbon::now()->addMinute(60),
            'messages' => [
                1 => [
                    $update->getMessage(),
                ]
            ],
        ];
        $conversation_service = new ConversationService();
        $conversation_service->putConversation($update->getChat()->getId(), $update->getFrom()->getId(), $convo);
    }
    
    public function nextStep($bot_message_id)
    {
        $convo = $this->getConversation();
        $convo['step'] = $convo['step'] + 1;
        $convo['reply_message_id'] = $bot_message_id;
        $convo['messages'][$convo['step']][] = $this->getUpdate()->getMessage();
        $this->getConversationService()->putConversation($this->getUpdate()->getChat()->getId(), $this->getUpdate()->getFrom()->getId(), $convo);
        
        return $this;
    }
    
    public function terminate()
    {
        return $this->getConversationService()->destroy($this->getUpdate()->getChat()->getId(), $this->getUpdate()->getFrom()->getId());
    }
    
    public function getMessages(int $step = null)
    {
        $convo = $this->getConversation();
        if (!isset($convo['messages'])) return [];
        if ($step == null) {
            return $convo['messages'];
        } else {
            if (!isset($convo['messages'][$step])) return [];
            return $convo['messages'][$step];
        }
    }
}