<?php

namespace Blaze\Myst\Controllers;

use Blaze\Myst\Api\Request\SendMessage;
use Blaze\Myst\Bot;
use Blaze\Myst\Api\Objects\Update;
use Blaze\Myst\Exceptions\MystException;
use Blaze\Myst\Services\ConfigService;
use Illuminate\Support\Facades\Cache;

abstract class ConversationController extends BaseController {

    protected $conversation;

    public function make(Bot $bot, Update $update)
    {
        try {
            $console = app()->runningInConsole();   // this will throw an error if not laravel
        }catch (\Throwable $e){
            throw new MystException("Conversations is not supported outside laravel.");
        }

        $this->setup($bot, $update);

        $this->conversation = $this->getConvo();

        $step = $this->getStep();
        if ($this->getStep() == 1){
            $this->init();
        }

        return $this->handle($step);
    }

    abstract public function handle($step);

    protected function init()
    {
        $convo = [
            'name'          => $this->getName(),
            'step'          => $this->getStep(),
            'steps'         => [],
            'reply_msg_id'  => null,
            'expires_at'    => now()->addMinutes(60)
        ];

        if (isset($this->conversation)){
            $this->bot->sendRequest(SendMessage::make()->text('There is an ongoing conversion of yours in this chat. Please terminate it before starting a new conversation.')->to($this->update()->getChat()->getId()));
            exit;
        }else{
            $this->saveConvo($convo);
        }
    }

    protected function getConvo()
    {
        return Cache::get(ConfigService::getConversationCacheKey())[$this->update->getChat()->getId()][$this->update->getMessage()->getFrom()->getId()];
    }

    protected function saveConvo(array $convo = null)
    {
        if ($convo == null) $convo = $this->conversation;
        $convos = Cache::get(ConfigService::getConversationCacheKey());
        $convos[$this->update->getChat()->getId()][$this->update->getMessage()->getFrom()->getId()] = $convo;
        Cache::forever(ConfigService::getConversationCacheKey(), $convos);
        $this->conversation = $convo;
    }

    public function getStep()
    {
        if (!isset($this->conversation)){
            return 1;
        }else{
            return $this->conversation['step'];
        }
    }

    public function setReplyMessageId($id){
        $this->conversation['reply_msg_id'] = $id;
        $this->saveConvo();

        return $this;
    }

    public function nextStep()
    {
        $current_step = $this->getStep();
        $current_step++;
        $this->conversation['step'] = $current_step;
        $this->saveConvo();

        return $this;
    }
    
    public function saveUpdate()
    {
        $this->conversation['steps'][$this->getStep()][] = $this->update->getRaw();
        $this->saveConvo();

        return $this;
    }

    public function getStepUpdates()
    {
        $steps = $this->conversation['steps'];
        $updates = [];
        foreach ($steps as $step_id => $step) {
            foreach ($step as $update) {
                $updates[$step_id][] = json_decode($update, true);
            }
        }
        return $updates;
    }

    public function terminate()
    {
        $convos = Cache::get(ConfigService::getConversationCacheKey());
        unset($convos[$this->update->getChat()->getId()][$this->update->getMessage()->getFrom()->getId()]);
        Cache::forever(ConfigService::getConversationCacheKey(), $convos);

        return $this;
    }
}