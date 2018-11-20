<?php

namespace Blaze\Myst\Controllers;

abstract class EntitiesController extends BaseController
{
    /**
     * @var array $aliases alternative names to this controller
     */
    protected $aliases = [];
    
    /**
     * @var string $position position of the entity inside the message
     */
    protected $position = '*';
    
    /**
     * @var bool $standalone whether or not the entity text should be the only text in the message
     */
    protected $standalone = true;
    
    /**
     * @var bool $case_sensitive whether or not the entity text should be case sensitive to the name (or aliases) of this controller
     */
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