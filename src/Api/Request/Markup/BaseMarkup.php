<?php

namespace Myst\Markup;

abstract class BaseMarkup
{
    protected $inline = false;

    protected function __construct($inline = false)
    {
        $this->inline = $inline;
    }

    public function isInline()
    {
        return $this->inline;
    }

    public function inline()
    {
        $this->inline = true;
        return $this;
    }

    public static function make($inline = false)
    {
        return new static($inline);
    }
}