<?php

namespace Blaze\Myst\Support\Laravel\Models;

use Illuminate\Database\Eloquent\Model;
use Blaze\Myst\Services\ConfigService;

class ChatMember extends Model
{
    protected $table = 'myst_chat_members';
    
    public function getConnectionName()
    {
        return ConfigService::getDatabaseConnection();
    }
    
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
        return $this->morphOne(Restriction::class, 'Restrictable', 'relative_type', 'relative_id', 'id');
    }
}