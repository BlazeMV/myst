<?php

namespace Blaze\Myst\Api\Requests\Markup;

class Button extends BaseMarkup
{
    protected $text;
    protected $field;
    protected $value;

    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }
    
    public function requestContact()
    {
        $this->field = 'request_contact';
        $this->value = true;
        return $this;
    }
    
    public function requestLocation()
    {
        $this->field = 'request_location';
        $this->value = true;
        return $this;
    }
    
    public function url($value)
    {
        $this->field = 'url';
        $this->value = $value;
        return $this;
    }
    
    public function callbackData($value)
    {
        $this->field = 'callback_data';
        $this->value = $value;
        return $this;
    }
    
    public function switchInlineQuery($value)
    {
        $this->field = 'switch_inline_query';
        $this->value = $value;
        return $this;
    }
    
    public function switchInlineQueryCurrentChat($value)
    {
        $this->field = 'switch_inline_query_current_chat';
        $this->value = $value;
        return $this;
    }
    
    public function callbackGame($value)
    {
        $this->field = 'callback_game';
        $this->value = $value;
        return $this;
    }
    
    public function pay($value)
    {
        $this->field = 'pay';
        $this->value = $value;
        return $this;
    }

    public function getData()
    {
        $data = [];
        $data['text'] = $this->text;
        if (isset($this->field) && isset($this->value)) {
            $data[$this->field] = $this->value;
        }
        return $data;
    }
}
