<?php

namespace Blaze\Myst\Api\Requests\Markup;

use Blaze\Myst\Exceptions\MarkupException;

class Keyboard extends BaseMarkup
{

    protected $rows = [];

    protected $resize = false;

    protected $one_time = false;

    protected $selective = false;

    protected $remove = false;
    
    /**
     * @param Button ...$buttons
     * @return $this
     * @throws MarkupException
     */
    public function addRow(Button ...$buttons)
    {
        $row = [];
        foreach ($buttons as $button) {
            if ($this->inline !== $button->inline) {
                if ($this->isInline()) {
                    throw new MarkupException("Custom keyboard button provided for inline keyboard.");
                } else {
                    throw new MarkupException("Inline keyboard button provided for custom keyboard.");
                }
            }
            $row[] = $button->getData();
        }
        $this->rows[] = $row;
        return $this;
    }
    
    /**
     * @return $this
     * @throws MarkupException
     */
    public function resize()
    {
        if ($this->isInline()) {
            throw new MarkupException("Resize an inline keyboard is not allowed.");
        }

        $this->resize = true;
        return $this;
    }
    
    /**
     * @return $this
     * @throws MarkupException
     */
    public function oneTime()
    {
        if ($this->isInline()) {
            throw new MarkupException("Sending a one time inline keyboard is not supported.");
        }

        $this->one_time = true;
        return $this;
    }
    
    /**
     * @return $this
     * @throws MarkupException
     */
    public function selective()
    {
        if ($this->isInline()) {
            throw new MarkupException("Sending an inline keyboard to selective people is not supported.");
        }

        $this->selective = true;
        return $this;
    }
    
    /**
     * @return $this
     * @throws MarkupException
     */
    public function remove()
    {
        if ($this->isInline()) {
            throw new MarkupException("Removing an inline keyboard is not supported.");
        }
        if (count($this->rows) > 0) {
            throw new MarkupException("This keyboard has existing buttons.");
        }

        $this->remove = true;
        return $this;
    }
    
    /**
     * @return false|string
     */
    public function serialize()
    {
        $data = [];
        if ($this->remove) {
            $data['remove_keyboard'] = $this->remove;
            $data['selective'] = $this->selective;
        } elseif ($this->isInline()) {
            $data['inline_keyboard'] = $this->rows;
        } else {
            $data['keyboard'] = $this->rows;
            $data['resize_keyboard'] = $this->resize;
            $data['one_time_keyboard'] = $this->one_time;
            $data['selective'] = $this->selective;
        }
        return json_encode($data);
    }
}
