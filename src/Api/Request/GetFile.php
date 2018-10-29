<?php

namespace Blaze\Myst\Api\Request;

use Blaze\Myst\Api\Objects\File;

class GetFile extends BaseRequest
{
    protected function responseObject()
    {
        return File::class;
    }
    
    
    public function id($file_id)
    {
        $this->params['file_id'] = $file_id;
        return $this;
    }
}