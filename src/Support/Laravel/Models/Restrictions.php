<?php

namespace Blaze\Myst\Support\Laravel\Models;

use Illuminate\Database\Eloquent\Model;

class Restrictions extends Model
{
    protected $table = 'myst_restrictions';
    
    public function Restrictable()
    {
        return $this->morphTo();
    }
}