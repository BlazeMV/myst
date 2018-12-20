<?php

namespace Blaze\Myst\Laravel\Models;

use Illuminate\Database\Eloquent\Model;
use Blaze\Myst\Api\Objects\User as TgUser;
use Blaze\Myst\Api\Objects\Chat as TgChat;
use Blaze\Myst\Services\ConfigService;

class User extends Model
{
    protected $table = 'myst_users';
    
    /**
     * @return \Illuminate\Config\Repository|mixed|string
     */
    public function getConnectionName()
    {
        return ConfigService::getDatabaseConnection();
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function chats()
    {
        return $this->belongsToMany(
            Chat::class,
            'myst_chat_members',
            'user_id',
            'chat_id'
        )->withPivot('admin')->withTimestamps();
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function chatMembers()
    {
        return $this->hasMany(ChatMember::class, 'user_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function restriction()
    {
        return $this->morphOne(Restriction::class, 'Restrictable', 'relative_type', 'relative_id', 'id');
    }
    
    /**
     * @param TgUser $user
     * @return $this
     */
    public function updateUser(TgUser $user)
    {
        $this->first_name = $user->getFirstName();
        $this->last_name = $user->getLastName();
        $this->username = $user->getUsername();
        $this->language_code = $user->getLanguageCode();
        $this->is_bot = $user->getIsBot();
        $this->save();
        return $this;
    }
    
    /**
     * @param $chat
     * @param TgChat $tg_chat
     */
    public function attachToChat($chat, TgChat $tg_chat)
    {
        if (!$this->Chats->contains($chat->id)) {
            // check if user is admin of chat
            if ($tg_chat->getType() == 'private') {
                $admin = true;
            } elseif ($tg_chat->getAllMembersAreAdministrators()) {
                $admin = true;
            } else {
                $admin = false;
            }
        
            //attach
            $this->chats()->attach($chat->id, ['admin' => $admin]);
        }
    }
    
    /**
     * @return string
     */
    public function getFullName()
    {
        return trim($this->first_name . " " . $this->last_name);
    }
    
    /**
     * @return string
     */
    public function getTaggableName()
    {
        if ($this->username !== null) {
            return "@" . $this->username;
        } else {
            return $this->getFullNameWithTag();
        }
    }
    
    /**
     * @return string
     */
    public function getFullNameWithTag()
    {
        return "<a href='tg://user?id=" . $this->id . "'>" . $this->getFullName() . "</a>";
    }
    
    /**
     * @param $chat_id
     * @return bool
     */
    public function isMemberOf($chat_id)
    {
        return $this->chatMembers()->where('chat_id', $chat_id)->exists();
    }
    
    /**
     * @param $chat_id
     * @return bool
     */
    public function isAdminOf($chat_id)
    {
        return $this->chatMembers()->where('chat_id', $chat_id)->where('admin', 1)->exists();
    }
}
