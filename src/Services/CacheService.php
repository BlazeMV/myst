<?php

namespace Blaze\Myst\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    protected $prefix;
    
    protected $chat_id;
    
    protected $user_id;
    
    protected $myst_cache;
    
    protected $cache;
    
    public function __construct($chat_id, $user_id)
    {
        $this->prefix = ConfigService::getConversationCacheKey();
        $this->chat_id = $chat_id;
        $this->user_id = $user_id;
    
        $this->myst_cache = Cache::get($this->prefix);
        $this->cache = $this->myst_cache[$chat_id][$user_id] ?? null;
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
        if (!$this->cache) return false;
        
        $this->cache['step'] = $this->cache['step'] + 1;
        $this->cache['steps'][] = $step;
        $this->cache['reply_msg_id'] = $message_id;
        
        return $this->saveCache();
    }
    
    public function init($name, $total_steps)
    {
        if ($this->cache) return false;
        
        $convo = [
            'name'          => $name,
            'step'          => $total_steps,
            'steps'         => [],
            'reply_msg_id'  => null,
            'expires_at'    => now()->addMinutes(60)
        ];
        
        $this->myst_cache[$this->chat_id][$this->user_id] = $convo;
        return $this->saveCache();
    }
    
    public function destroy()
    {
        unset($this->myst_cache[$this->chat_id][$this->user_id]);
        return $this->saveCache();
    }
    
    protected function saveCache()
    {
        $this->myst_cache[$this->chat_id][$this->user_id] = $this->cache;
        Cache::forever($this->prefix, $this->myst_cache);
        
        return true;
    }
    
    protected function removeExpired()
    {
        foreach ($this->myst_cache as $chat_id => $chat) {
            foreach ($chat as $user_id => $user) {
                if (Carbon::parse($user['expires_at'])->greaterThanOrEqualTo(Carbon::now())) unset($this->myst_cache[$chat_id][$user_id]);
            }
            if (count($chat) < 1) unset($this->myst_cache[$chat_id]);
        }
        return $this->saveCache();
    }
}