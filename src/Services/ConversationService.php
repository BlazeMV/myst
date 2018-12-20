<?php

namespace Blaze\Myst\Services;

class ConversationService
{
    /**
     * @var CacheService $cache_service
     */
    protected $cache_service;
    
    /**
     * ConversationService constructor.
     */
    public function __construct()
    {
        $this->cache_service = new CacheService();
    }
    
    /**
     * @return CacheService
     */
    protected function getCacheService()
    {
        return $this->cache_service;
    }
    
    /**
     * Check whether the chat & user has an active conversation
     *
     * @param $chat
     * @param $user
     * @return bool
     */
    public function hasConversation($chat, $user)
    {
        $myst_cache = $this->getCacheService()->getMystConversationsCache();
        return isset($myst_cache[$chat][$user]) && count($myst_cache[$chat][$user]) > 0;
    }
    
    /**
     * Fetch an active conversation by chat & user
     *
     * @param $chat
     * @param $user
     * @return null
     */
    public function getConversation($chat, $user)
    {
        $myst_cache = $this->getCacheService()->getMystConversationsCache();
        return $myst_cache[$chat][$user] ?? null;
    }
    
    /**
     * Put data to a new or an existing conversation cache
     *
     * @param $chat
     * @param $user
     * @param $data
     */
    public function putConversation($chat, $user, $data)
    {
        $myst_cache = $this->getCacheService()->getMystConversationsCache();
        $myst_cache[$chat][$user] = $data;
    
        $this->getCacheService()->saveMystConversationsCache($myst_cache);
    }
    
    /**
     * Delete an active conversation from cache
     *
     * @param $chat
     * @param $user
     * @return bool
     */
    public function destroy($chat, $user)
    {
        if (!$this->hasConversation($chat, $user)) {
            return false;
        }
        
        $myst_cache = $this->getCacheService()->getMystConversationsCache();
        unset($myst_cache[$chat][$user]);
        
        $this->getCacheService()->saveMystConversationsCache($myst_cache);
        
        return true;
    }
}
