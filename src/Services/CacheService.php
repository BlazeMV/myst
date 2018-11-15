<?php

namespace Blaze\Myst\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    protected $prefix;
    
    protected $chat_id;
    
    protected $user_id;
    
    public function __construct($chat_id, $user_id)
    {
        $this->prefix = ConfigService::getConversationCacheKey();
        $this->chat_id = $chat_id;
        $this->user_id = $user_id;
    }
    
    protected function getMystCache()
    {
        return Cache::get($this->prefix);
    }
    
    public function getCache()
    {
        return $this->getMystCache()[$this->chat_id][$this->user_id] ?? null;
    }
    
    public function getName()
    {
        return $this->cache['name'] ?? null;
    }
    
    public function getCurrentStep()
    {
        return $this->cache['step'] ?? null;
    }
    
    public function getSteps()
    {
        return $this->cache['steps'] ?? null;
    }
    
    public function getStep($step)
    {
        return $this->cache['steps'][$step] ?? null;
    }
    
    public function expiresAt($step)
    {
        return $this->cache['expires_at'][$step] ?? null;
    }
    
    public function forward($step, $message_id)
    {
        if (!$this->getCache()) return false;
        
        $this->getCache()['step'] = $this->getCache()['step'] + 1;
        $this->getCache()['steps'][] = $step;
        $this->getCache()['reply_msg_id'] = $message_id;
        
        return $this->saveCache();
    }
    
    public function init($name)
    {
        if ($this->getCache()) return false;
        
        $convo = [
            'name'          => $name,
            'step'          => 0,
            'steps'         => [],
            'reply_msg_id'  => null,
            'expires_at'    => now()->addMinutes(60)
        ];
        
        $this->getMystCache()[$this->chat_id][$this->user_id] = $convo;
        return $this->saveCache();
    }
    
    public function destroy()
    {
        unset($this->getMystCache()[$this->chat_id][$this->user_id]);
        return $this->saveCache();
    }
    
    protected function saveCache()
    {
        $this->getMystCache()[$this->chat_id][$this->user_id] = $this->getCache();
        Cache::forever($this->prefix, $this->getMystCache());
        
        return true;
    }
    
    protected function removeExpired()
    {
        foreach ($this->getMystCache() as $chat_id => $chat) {
            foreach ($chat as $user_id => $user) {
                if (Carbon::parse($user['expires_at'])->greaterThanOrEqualTo(Carbon::now())) unset($this->getMystCache()[$chat_id][$user_id]);
            }
            if (count($chat) < 1) unset($this->getMystCache()[$chat_id]);
        }
        return $this->saveCache();
    }
}