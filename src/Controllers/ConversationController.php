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
        return $this;
    }
    
    protected function getConversationService()
    {
        return $this->conversation_service;
    }
    
    public function getConversation()
    {
        return $this->getConversationService()->getConversation($this->getUpdate()->getChat()->getId(), $this->getUpdate()->getFrom()->getId());
    }
    
    public static function init(Update $update)
    {
        $self = new static();
        $convo = [
            'name' => $self->getName(),
            'step' => 1,
            'reply_message_id' => null,
            'expires_at' => Carbon::now()->addMinute(60),
            'messages' => [
            
            ],
        ];
        $conversation_service = new ConversationService();
        $conversation_service->putConversation($update->getChat()->getId(), $update->getFrom()->getId(), $convo);
        
        return $self->make($update->getBot(), $update, $convo);
    }
    
    protected function handle($step)
    {
        return $this->{'step' . $step}();
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