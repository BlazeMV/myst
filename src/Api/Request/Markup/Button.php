<?php

namespace Myst\Markup;

use Blaze\Myst\Exceptions\MarkupException;

class Button extends BaseMarkup
{
    protected $text;
    protected $field;
    protected $value;

    protected $accepted_fields = ['request_contact', 'request_location', 'url', 'callback_data', 'switch_inline_query', 'switch_inline_query_current_chat', 'callback_game', 'pay'];

    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    public function setField($field)
    {
        if (!in_array($field, $this->accepted_fields)) throw new MarkupException("$field is not an accepted field type.");
        $this->field = $field;
        return $this;
    }

    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    public function getData()
    {
        $data = [];
        $data['text'] = $this->text;
        if (isset($this->field) && isset($this->value)){
            $data[$this->field] = $this->value;
        }
        return $data;
    }
}