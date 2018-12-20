<?php

namespace Blaze\Myst\Laravel\Models;

use Illuminate\Database\Eloquent\Model;
use Blaze\Myst\Services\ConfigService;

class Restriction extends Model
{
    protected $table = 'myst_restrictions';
    
    /**
     * @return \Illuminate\Config\Repository|mixed|string
     */
    public function getConnectionName()
    {
        return ConfigService::getDatabaseConnection();
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function restrictable()
    {
        return $this->morphTo();
    }
}
