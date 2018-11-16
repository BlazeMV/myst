<?php

namespace Blaze\Myst\Services;

class ConversationService
{
    protected $cache_service;
    
    public function __construct()
    {
        $this->cache_service = new CacheService();
    }
    
    protected function getCacheService()
    {
        return $this->cache_service;
    }
    
    public function hasConversation($chat, $user)
    {
        $myst_cache = $this->getCacheService()->getMystConversationsCache();
        return isset($myst_cache[$chat][$user]) && count($myst_cache[$chat][$user]) > 0;
    }
    
    public function getConversation($chat, $user)
    {
        $myst_cache = $this->getCacheService()->getMystConversationsCache();
        return $myst_cache[$chat][$user] ?? null;
    }
    
    public function putConversation($chat, $user, $data)
    {
        $myst_cache = $this->getCacheService()->getMystConversationsCache();
        $myst_cache[$chat][$user] = $data;
    
        $this->getCacheService()->saveMystConversationsCache($myst_cache);
    }
    
    public function destroy($chat, $user)
    {
        if (!$this->hasConversation($chat, $user)) return true;
        
        $myst_cache = $this->getCacheService()->getMystConversationsCache();
        unset($myst_cache[$chat][$user]);
        
        $this->getCacheService()->saveMystConversationsCache($myst_cache);
    }
}