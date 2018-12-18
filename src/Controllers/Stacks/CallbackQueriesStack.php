<?php

namespace Blaze\Myst\Controllers\Stacks;

use Blaze\Myst\Api\Objects\Update;
use Blaze\Myst\Bot;
use Blaze\Myst\Controllers\BaseController;
use Blaze\Myst\Controllers\CallbackQueryController;
use Blaze\Myst\Exceptions\StackException;
use Blaze\Myst\Helpers\Arr;

class CallbackQueriesStack extends BaseStack
{
    /**
     * @inheritdoc
     */
    public function addStackItem(BaseController $item): BaseController
    {
        if (!$item instanceof CallbackQueryController) throw new StackException(get_class($item) . " must be an instance of " . CallbackQueryController::class);
        if (array_has($this->items, $item->getName())) throw new StackException($item->getName() . " has already been registered as a callback query.");
        $this->items[$item->getName()] = $item;
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
    
        foreach ($this->getStack() as $name => $cbq) {
            /** @var CallbackQueryController $cbq */
        
            $text = $this->checkItemPrerequisites($update, $cbq, $name);
        
            if (!$text) {
                continue;
            }
    
            $pos = strpos($text, $name);
            if ($pos !== false) {
                $text = substr_replace($text, '', $pos, strlen($name));
            }
            $cbq->setArguments($text, $bot->getConfig('cbq_param_separator'))->make($update);
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
        if ($bot->getConfig('process.callback_queries') == false)  {
            return false;
        }
    
        $type = $update->detectType();
        if ($type !== 'callback_query') {
            return false;
        }
    
        return true;
    }
    
    protected function checkItemPrerequisites(Update $update, CallbackQueryController $cbq, $name)
    {
        if (!Arr::isValueTrue($cbq->getEngagesIn(), $update->getChat()->getType())) {
            return false;
        }
    
        $text = $update->getCallbackQuery()->has('data') ? $update->getCallbackQuery()->getData() : $update->getCallbackQuery()->getGameShortName();
        if ($text !== $name && !starts_with($text, $name . " ")) {
            return false;
        }
        
        return $text;
    }
}