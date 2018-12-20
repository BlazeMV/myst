<?php

namespace Blaze\Myst\Controllers;

abstract class TextController extends EntitiesController
{
    /**
     * @param string $text
     * @param int $offset
     * @param int $length
     * @return bool
     */
    public function inPosition(string $text, int $offset, int $length)
    {
        $position = $this->getPosition();
        switch ($position) {
            case 'any':
            case '*':
                return true;
                break;
            case 'start':
            case 0:
                if ($offset == 0) {
                    return true;
                } else {
                    return false;
                }
                break;
            case 'end':
                if (strlen($text) == ($offset + $length)) {
                    return true;
                } else {
                    return false;
                }
                break;
            default:
                if ((int)$position == $offset) {
                    return true;
                } else {
                    return false;
                }
        }
    }
}
