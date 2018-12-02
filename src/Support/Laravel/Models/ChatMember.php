<?php

namespace Blaze\Myst\Support\Laravel\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMember extends Model
{
    protected $table = 'myst_chat_members';
    
    public function User()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    
    public function Chat()
    {
        return $this->belongsTo(Chat::class, 'chat_id', 'id');
    }
    
    public function Restriction()
    {
        return $this->morphOne(Restrictions::class, 'Restrictable');
    }
}