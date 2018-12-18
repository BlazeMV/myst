<?php

namespace Blaze\Myst\Controllers\Stacks;

use Blaze\Myst\Api\Objects\Update;
use Blaze\Myst\Bot;
use Blaze\Myst\Controllers\BaseController;
use Blaze\Myst\Controllers\TextController;
use Blaze\Myst\Exceptions\StackException;
use Blaze\Myst\Helpers\Arr;
use Blaze\Myst\Helpers\Str;

class TextsStack extends BaseStack
{
    /**
     * @inheritdoc
     */
    public function addStackItem(BaseController $item): BaseController
    {
        if (!$item instanceof TextController) throw new StackException(get_class($item) . " must be an instance of " . TextController::class);
        $names = array_merge($item->getAliases(), [$item->getName()]);
        foreach ($names as $name) {
            if (array_has($this->items, $name)) throw new StackException("$name has already been registered as a text.");
            $this->items[$name] = $item;
        }
        return $item;
    }
    
    /**
     * @param Update $update
     * @return bool|mixed
     * @throws \Blaze\Myst\Exceptions\ConfigurationException
     */
    public function processStack(Update $update)
    {
        $bot = $update->getBot();
        
        if (!$this->checkStackPrerequisites($bot, $update)) {
            return false;
        }
        
        foreach ($this->getStack() as $name => $text) {
            /** @var TextController $text */
            
            $ok = $this->checkItemPrerequisites($update, $text, $name);
            
            if (!$ok) {
                continue;
            }
            
            $text->make($update);
        }
        
        return true;
    }
    
    /**
     * @param Bot $bot
     * @param Update $update
     * @return bool
     * @throws \Blaze\Myst\Exceptions\ConfigurationException
     */
    protected function checkStackPrerequisites(Bot $bot, Update $update): bool
    {
        if ($bot->getConfig('process.texts') == false)  {
            return false;
        }
        
        $type = $update->detectType();
        if ($type !== 'message' && $type !== 'edited_message' && $type !== 'channel_post' && $type !== 'edited_channel_post') {
            return false;
        }
        
        return true;
    }
    
    /**
     * @param Update $update
     * @param TextController $text
     * @param $name
     * @return bool|string
     */
    protected function checkItemPrerequisites(Update $update, TextController $text, $name)
    {
        if (!Arr::isValueTrue($text->getEngagesIn(), $update->getChat()->getType())) {
            return false;
        }
        
        $content = $update->getMessage()->getText();
        if ($text->isStandalone() && !Str::compareCaseInsensitive($name, $content)) {
            return false;
        }
        if ($text->isStandalone() && $text->isCaseSensitive() && $name !== $content) {
            return false;
        }
        if ($text->isCaseSensitive() && strpos($content, $name) === false) {
            return false;
        }
        if (strpos(strtolower($content), strtolower($name)) === false) {
            return false;
        }
        
        //add entity in position
        
        return true;
    }
}