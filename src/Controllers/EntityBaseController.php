<?php

namespace Blaze\Myst\Controllers;

abstract class EntityBaseController extends BaseController
{
    
    protected $aliases = [];
    
    protected $position = '*';
    
    protected $standalone = true;
    
    protected $case_sensitive = false;
    
    /**
     * @return array
     */
    public function getAliases(): array
    {
        return $this->aliases;
    }
    
    /**
     * @param array $aliases
     * @return EntityBaseController
     */
    public function setAliases(array $aliases): EntityBaseController
    {
        $this->aliases = $aliases;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getPosition(): string
    {
        return $this->position;
    }
    
    /**
     * @param string $position
     * @return EntityBaseController
     */
    public function setPosition(string $position): EntityBaseController
    {
        $this->position = $position;
        return $this;
    }
    
    /**
     * @return bool
     */
    public function isStandalone(): bool
    {
        return $this->standalone;
    }
    
    /**
     * @param bool $standalone
     * @return EntityBaseController
     */
    public function setStandalone(bool $standalone): EntityBaseController
    {
        $this->standalone = $standalone;
        return $this;
    }
    
    /**
     * @return bool
     */
    public function isCaseSensitive(): bool
    {
        return $this->case_sensitive;
    }
    
    /**
     * @param bool $case_sensitive
     * @return EntityBaseController
     */
    public function setCaseSensitive(bool $case_sensitive): EntityBaseController
    {
        $this->case_sensitive = $case_sensitive;
        return $this;
    }
}