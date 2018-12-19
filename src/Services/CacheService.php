<?php

namespace Blaze\Myst\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    public function __construct()
    {
        $this->removeExpiredMystConversations();
    }
    
    public function getMystConversationsCache()
    {
        return Cache::get(ConfigService::getConversationCacheKey());
    }
    
    public function saveMystConversationsCache(array $data)
    {
        Cache::forever(ConfigService::getConversationCacheKey(), $data);
    }
    
    protected function removeExpiredMystConversations()
    {
        $myst_conversations = $this->getMystConversationsCache();
        foreach ($myst_conversations as $chat_id => $chat) {
            foreach ($chat as $user_id => $user) {
                if (Carbon::parse($user['expires_at'])->lessThanOrEqualTo(Carbon::now())) unset($myst_conversations[$chat_id][$user_id]);
            }
            if (count($chat) < 1) unset($myst_conversations[$chat_id]);
        }
        $this->saveMystConversationsCache($myst_conversations);
    }
}
