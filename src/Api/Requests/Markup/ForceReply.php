<?php

namespace Blaze\Myst\Api\Requests\Markup;

class ForceReply
{
    /**
     * @var bool $selective
    */
    protected $selective = false;
    
    /**
     * @return $this
     */
    public function selective()
    {
        $this->selective = true;
        return $this;
    }
    
    /**
     * @return ForceReply
     */
    public static function make()
    {
        return new self;
    }
    
    /**
     * @return false|string
     */
    public function serialize()
    {
        $data = [];
        $data['force_reply'] = true;
        $data['selective'] = $this->selective;
        return json_encode($data);
    }
}
