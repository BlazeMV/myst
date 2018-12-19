<?php

namespace Blaze\Myst\Controllers;

abstract class CallbackQueryController extends BaseController
{
    /** @var array $arguments */
    protected $arguments;
    
    /**
     * @return array
     */
    public function getArguments() : array
    {
        return $this->arguments;
    }
    
    /**
     * @param string $text
     * @param string $separator
     * @return CallbackQueryController
     */
    public function setArguments(string $text, string $separator) : CallbackQueryController
    {
        if (!starts_with($separator, 'regex:')) {
            $separator = '/([^' . $separator . ']+)/';
        } else {
            $separator = str_replace('regex:', '', $separator);
        }
        
        preg_match_all($separator, $text, $matches);
        $this->arguments = $matches[1] ?? [];
        
        return $this;
    }
}
