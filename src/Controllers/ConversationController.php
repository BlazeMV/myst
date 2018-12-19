<?php

namespace Blaze\Myst\Controllers;

use Blaze\Myst\Api\Objects\Update;
use Blaze\Myst\Bot;
use Blaze\Myst\Services\ConversationService;
use Carbon\Carbon;

abstract class ConversationController extends BaseController
{
    protected $step;
    
    /**
     * @var ConversationService $conversation_service
    */
    protected $conversation_service;
    
    public function make(Update $update)
    {
        $this->setup($update);
        
        if ($this->conversation_service == null) $this->conversation_service = new ConversationService();
            
        $this->step = $this->getConversation()['step'];
    
        $this->handle();
    
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
        $self->conversation_service = new ConversationService();
        $self->conversation_service->putConversation($update->getChat()->getId(), $update->getFrom()->getId(), $convo);
        
        return $self->make($update);
    }
    
    protected function handle()
    {
        $step = $this->getStep();
        return $this->{'step' . $step}();
    }
    
    public function nextStep($bot_message_id)
    {
        $convo = $this->getConversation();
        $convo['reply_message_id'] = $bot_message_id;
        $convo['messages'][$convo['step']][] = $this->getUpdate()->getMessage();
        $convo['step'] = $convo['step'] + 1;
        $this->getConversationService()->putConversation($this->getUpdate()->getChat()->getId(), $this->getUpdate()->getFrom()->getId(), $convo);
        
        return $this;
    }
    
    public function getStep()
    {
        return $this->step;
    }
    
    public function saveResponse()
    {
        $convo = $this->getConversation();
        $convo['messages'][$convo['step']][] = $this->getUpdate()->getMessage();
        $this->getConversationService()->putConversation($this->getUpdate()->getChat()->getId(), $this->getUpdate()->getFrom()->getId(), $convo);
        
        return $this;
    }
    
    public function terminate()
    {
        return $this->getConversationService()->destroy($this->getUpdate()->getChat()->getId(), $this->getUpdate()->getFrom()->getId());
    }
    
    public function getResponses(int $step = null)
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
