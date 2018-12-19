<?php

namespace Blaze\Myst\Api\Requests\Markup;

class ForceReply {

    protected $selective = false;

    public function selective(){
        $this->selective = true;
        return $this;
    }

    public static function make(){
        return new self;
    }

    public function serialize(){
        $data = [];
        $data['force_reply'] = true;
        $data['selective'] = $this->selective;
        return json_encode($data);
    }
}
