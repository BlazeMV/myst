<?php

namespace Blaze\Myst\Laravel\Models;

use Illuminate\Database\Eloquent\Model;
use Blaze\Myst\Services\ConfigService;

class ChatMember extends Model
{
    protected $table = 'myst_chat_members';
    
    /**
     * @return \Illuminate\Config\Repository|mixed|string
     */
    public function getConnectionName()
    {
        return ConfigService::getDatabaseConnection();
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chat()
    {
        return $this->belongsTo(Chat::class, 'chat_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function restriction()
    {
        return $this->morphOne(Restriction::class, 'Restrictable', 'relative_type', 'relative_id', 'id');
    }
}
