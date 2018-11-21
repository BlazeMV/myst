<?php

namespace Blaze\Myst\Support\Laravel\Models;

use Illuminate\Database\Eloquent\Model;
use Blaze\Myst\Api\Objects\User as TgUser;

class User extends Model
{
    protected $table = 'myst_users';
    
    public function Chats()
    {
        return $this->belongsToMany(Chat::class, 'myst_chat_members', 'user_id', 'chat_id')->as('user_chat')->withTimestamps();
    }
    
    public function ChatMembers()
    {
        return $this->hasMany(ChatMember::class, 'user_id', 'id');
    }
    
    public function updateUser(TgUser $user){
        $this->first_name = $user->getFirstName();
        $this->last_name = $user->getLastName();
        $this->username = $user->getUsername();
        $this->language_code = $user->getLanguageCode();
        $this->is_bot = $user->getIsBot();
        $this->save();
        return $this;
    }
    
    public function getFullName()
    {
        return trim($this->first_name . " " . $this->last_name);
    }
    
    public function getTaggableName()
    {
        if ($this->username !== null) {
            return "@" . $this->username;
        } else {
            return $this->getFullNameWithTag();
        }
    }
    
    public function getFullNameWithTag()
    {
        return "<a href='tg://user?id=" . $this->id . "'>" . $this->getFullName() . "</a>";
    }
    
    public function isMemberOf($chat_id)
    {
        return $this->ChatMembers()->where('chat_id', $chat_id)->exists();
    }
    
    public function isAdminOf($chat_id)
    {
        return $this->ChatMembers()->where('chat_id', $chat_id)->where('admin', 1)->exists();
    }
}