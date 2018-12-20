<?php

namespace Blaze\Myst\Api\Requests;

class AnswerCallbackQuery extends BaseRequest
{
    /**
     * @return string
     */
    protected function responseObject() : string
    {
        return 'bool';
    }
    
    /**
     * @param $callback_query_id
     * @return $this
     */
    public function to($callback_query_id)
    {
        $this->params['callback_query_id'] = $callback_query_id;
        return $this;
    }
    
    /**
     * @param $text
     * @return $this
     */
    public function text($text)
    {
        $this->params['text'] = $text;
        return $this;
    }
    
    /**
     * @return $this
     */
    public function alert()
    {
        $this->params['show_alert'] = true;
        return $this;
    }
    
    /**
     * @param $url
     * @return $this
     */
    public function url($url)
    {
        $this->params['url'] = $url;
        return $this;
    }
    
    /**
     * @param $time
     * @return $this
     */
    public function cache($time)
    {
        $this->params['cache_time'] = $time;
        return $this;
    }
}
