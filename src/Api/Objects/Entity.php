<?php

namespace Blaze\Myst\Api\Objects;

use Blaze\Myst\Api\ApiObject;

/**
 * @method int getType()
 * @method int getOffset()
 * @method int getLength()
 * @method int getUrl()
 * @method int getUser()
 */
class Entity extends ApiObject
{
    protected function singleObjectRelations(): array
    {
        return [
            'user' => User::class
        ];
    }
    
    public function getText($text)
    {
        return substr($text, $this->getOffset(), $this->getLength());
    }
    
    public function inPosition($text, $position)
    {
        switch ($position) {
            case 'any':
            case '*':
                return true;
                break;
            case 'start':
            case 0:
                if ($this->getOffset() == 0) {
                    return true;
                } else {
                    return false;
                }
                break;
            case 'end':
                if (strlen($text) == ($this->getOffset() + $this->getLength())) {
                    return true;
                } else {
                    return false;
                }
                break;
            default:
                if ((int)$position == $this->getOffset()) {
                    return true;
                } else {
                    return false;
                }
        }
    }
}