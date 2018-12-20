<?php

namespace Blaze\Myst\Laravel\Models;

use Blaze\Myst\Api\Objects\Chat as TgChat;
use Blaze\Myst\Services\ConfigService;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $table = 'myst_chats';
    
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
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'myst_chat_members',
            'chat_id',
            'user_id'
        )->withPivot('admin')->withTimestamps();
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function chatMembers()
    {
        return $this->hasMany(ChatMember::class, 'chat_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function restriction()
    {
        return $this->morphOne(Restriction::class, 'Restrictable', 'relative_type', 'relative_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function admins()
    {
        return $this->chatMembers()->where('admin', 1);
    }
    
    /**
     * @param $user_id
     * @return bool
     */
    public function hasMember($user_id)
    {
        return $this->chatMembers()->where('user_id', $user_id)->exists();
    }
    
    /**
     * @param $user_id
     * @return bool
     */
    public function hasAdmin($user_id)
    {
        return $this->admins()->where('user_id', $user_id)->exists();
    }
    
    /**
     * @param $user_id
     * @return $this
     */
    public function makeAdmin($user_id)
    {
        $this->chatMembers()->where('user_id', $user_id)->update(['admin', 1]);
        return $this;
    }
    
    /**
     * @param $user_id
     * @return $this
     */
    public function removeAdmin($user_id)
    {
        $this->chatMembers()->where('user_id', $user_id)->update(['admin', 0]);
        return $this;
    }
    
    /**
     * @param TgChat $chat
     * @return $this
     */
    public function updateChat(TgChat $chat)
    {
        $this->type = $chat->getType();
        $this->title = $chat->getTitle() === null
            ? ($chat->getFirstName() . ($chat->getLastName() === null ? "" : " " . $chat->getLastName()))
            : $chat->getTitle();
        $this->username = $chat->getUsername();
        $this->first_name = $chat->getFirstName();
        $this->last_name = $chat->getLastName();
        $this->all_members_are_administrators = $chat->getAllMembersAreAdministrators();
        $this->save();
        return $this;
    }
    
    /**
     * @return mixed|string
     */
    public function getName()
    {
        return $this->title !== null
            ? $this->title
            : $this->first_name . ($this->last_name !== null ? " " . $this->last_name : "");
    }
}
