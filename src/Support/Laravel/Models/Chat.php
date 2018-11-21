<?php

namespace Blaze\Myst\Support\Laravel\Models;

use Blaze\Myst\Api\Objects\Chat as TgChat;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $table = 'myst_chats';
    
    public function Users()
    {
        return $this->belongsToMany(User::class, 'myst_chat_members', 'chat_id', 'user_id')->as('chat_member')->withTimestamps();
    }
    
    public function ChatMembers()
    {
        return $this->hasMany(ChatMember::class, 'chat_id', 'id');
    }
    
    public function Admins(){
        return $this->ChatMembers()->where('admin', 1);
    }
    
    public function hasMember($user_id){
        return $this->ChatMembers()->where('user_id', $user_id)->exists();
    }
    
    public function hasAdmin($user_id){
        return $this->Admins()->where('user_id', $user_id)->exists();
    }
    
    public function makeAdmin($user_id){
        $this->ChatMembers()->where('user_id', $user_id)->update(['admin', 1]);
        return $this;
    }
    
    public function removeAdmin($user_id){
        $this->ChatMembers()->where('user_id', $user_id)->update(['admin', 0]);
        return $this;
    }
    
    public function updateChat(TgChat $chat){
        $this->type = $chat->getType();
        $this->title = $chat->getTitle() === null ? ($chat->getFirstName() . ($chat->getLastName() === null ? "" : " " . $chat->getLastName())) : $chat->getTitle();
        $this->username = $chat->getUsername();
        $this->first_name = $chat->getFirstName();
        $this->last_name = $chat->getLastName();
        $this->all_members_are_administrators = $chat->getAllMembersAreAdmin();
        $this->save();
        return $this;
    }
    
    public function getName(){
        return $this->title !== null ? $this->title : $this->first_name . ($this->last_name !== null ? " " . $this->last_name : "");
    }
}