<?php

namespace Blaze\Myst\Laravel\Models;

use Illuminate\Database\Eloquent\Model;
use Blaze\Myst\Services\ConfigService;

class Restriction extends Model
{
    protected $table = 'myst_restrictions';
    
    public function getConnectionName()
    {
        return ConfigService::getDatabaseConnection();
    }
    
    public function Restrictable()
    {
        return $this->morphTo();
    }
}
