<?php

namespace Blaze\Myst\Controllers;

abstract class EntitiesController extends BaseController
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
     * @return EntitiesController
     */
    public function setAliases(array $aliases): EntitiesController
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
     * @return EntitiesController
     */
    public function setPosition(string $position): EntitiesController
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
     * @return EntitiesController
     */
    public function setStandalone(bool $standalone): EntitiesController
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
     * @return EntitiesController
     */
    public function setCaseSensitive(bool $case_sensitive): EntitiesController
    {
        $this->case_sensitive = $case_sensitive;
        return $this;
    }
}