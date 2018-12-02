<?php

namespace Blaze\Myst\Api\Objects;

use Blaze\Myst\Api\ApiObject;
use Blaze\Myst\Api\Requests\GetChatAdministrators;
use Blaze\Myst\Api\Requests\SendMessage;
use Blaze\Myst\Api\Response;
use Blaze\Myst\Bot;
use Blaze\Myst\Controllers\CallbackQueryController;
use Blaze\Myst\Controllers\CommandController;
use Blaze\Myst\Controllers\HashtagController;
use Blaze\Myst\Controllers\MentionController;
use Blaze\Myst\Controllers\TextController;
use Blaze\Myst\Services\ConversationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Blaze\Myst\Support\Laravel\Models\User as MystUser;
use Blaze\Myst\Support\Laravel\Models\Chat as MystChat;
use Blaze\Myst\Support\Laravel\Models\ChatMember as MystChatMember;

/**
 * @method CallBackQuery getCallbackQuery()
 */
class Update extends ApiObject
{
    /**
     * @var Bot $bot
    */
    protected $bot;
    
    public function setBot(Bot $bot)
    {
        $this->bot = $bot;
        return $this;
    }
    
    public function getBot()
    {
        return $this->bot;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function singleObjectRelations(): array
    {
        return [
            'message'              => Message::class,
            'edited_message'       => Message::class,
            'channel_post'         => Message::class,
            'edited_channel_post'  => Message::class,
//            'inline_query'         => InlineQuery::class,
//            'chosen_inline_result' => ChosenInlineResult::class,
            'callback_query'       => CallbackQuery::class,
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    protected function multipleObjectRelations(): array
    {
        return [
        
        ];
    }
    
    /**
     * Determine if the update is of given type
     *
     * @param string         $type
     *
     * @return bool
     */
    public function isType($type)
    {
        if ($this->has(strtolower($type))) {
            return true;
        }
        
        return $this->detectType() === $type;
    }
    
    /**
     * Detect type based on properties.
     *
     * @return string|null
     */
    public function detectType()
    {
        $types = [
            'message',
            'edited_message',
            'channel_post',
            'edited_channel_post',
            'inline_query',
            'chosen_inline_result',
            'callback_query',
            'shipping_query',
            'pre_checkout_query',
        ];
        
        return $this->keys()->intersect($types)->pop();
    }
    
    /**
     * Get the message contained in the Update.
     *
     * @return Message
     */
    public function getMessage()
    {
        switch ($this->detectType()) {
            case 'message':
                return $this->get('message');
            case 'edited_message':
                return $this->get('edited_message');
            case 'channel_post':
                return $this->get('channel_post');
            case 'edited_channel_post':
                return $this->get('edited_channel_post');
            case 'callback_query':
                $callbackQuery = $this->get('callback_query');
                if ($callbackQuery->has('message')) {
                    return $callbackQuery->get('message');
                }
                break;
            default:
                return null;
        }
    
        return null;
    }
    
    /**
     * Get chat object
     *
     * @return Chat
     */
    public function getChat()
    {
        /*if ($this->isType('message') || $this->isType('edited_message')) return $this->getMessage()->getChat();
        if ($this->isType('callback_query')) return $this->getCallbackQuery()->getMessage()->getChat();
        return null;*/
        
        return $this->getMessage() === null ? null : $this->getMessage()->getChat();
    }
    
    public function getFrom()
    {
        return $this->getMessage() === null ? null : $this->getMessage()->getFrom();
    }
    
    protected function entityInPosition($text, $position, $offset, $length)
    {
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
    
    protected function getArgs($text, $separator)
    {
        if (!starts_with($separator, 'regex:')) {
            $separator = '/([^' . $separator . ']+)/';
        } else {
            $separator = str_replace('regex:', '', $separator);
        }
        
        preg_match_all($separator, $text, $matches);
        return $matches[1];
    }
    
    
    public function processUpdate()
    {
        $this->updateDatabase();
        
        if ($this->hasRestrictions()) return $this;
        
        if ($this->detectType() == ('edited_message' || 'edited_channel_post') && $this->bot->getConfig('process_edited_messages') == false) return $this;
        
        if (!$this->bot->getConfig('engages_in.' . $this->getChat()->getType())) return $this;
        
        $this->processConversations();
        $this->processCommands();
        $this->processCallbackQueries();
        $this->processHashtags();
        $this->processMentions();
        $this->processTexts();
        
        return $this;
    }
    
    protected function updateDatabase()
    {
        /** @var MystUser $user
         * @var MystChat $chat
         * @var MystChatMember $chat_member
         */
        
        // check for table existence
        if (!Schema::hasTable('myst_users')) return true;
        if (!Schema::hasTable('myst_chats')) return true;
        if (!Schema::hasTable('myst_chat_members')) return true;
        
        // create new / update existing user
        $user = MystUser::find($this->getFrom()->getId());
        if ($user == null) {
            $user = new MystUser();
            $user->id = $this->getFrom()->getId();
        }
        $user = $user->updateUser($this->getFrom());
    
        // create new / update existing chat
        $chat = MystChat::find($this->getChat()->getId());
        if ($chat == null) {
            $chat = new MystChat();
            $chat->id = $this->getChat()->getId();
        }
        $chat = $chat->updateChat($this->getChat());
    
        // attach user to a chat (create a new chat member) if not already attached
        $user->attachToChat($chat, $this->getChat());
    
        // update chat admins every 24 hours
        if (Carbon::parse(MystChatMember::where('chat_id', $this->getChat()->getId())->orderBy('created_at')->first()->updated_at)->lessThan(Carbon::parse('-24 hours'))) {
    
            $this->getBot()->sendRequest(GetChatAdministrators::make()->chat($this->getChat()->getId())->async(false), function (Response $response) {
                if ($response->isOk()) {
                    $admins = $response->getResponseObject();
                    foreach ($admins as $admin) {
                        if ($admin->getStatus() == "creator" || $admin->getStatus() == "administrator") {
                            ChatMember::where('chat_id', $this->getChat()->getId())->where('user_id', $admin->getUser()->getId())->update(['admin', true]);
                        } else {
                            ChatMember::where('chat_id', $this->getChat()->getId())->where('user_id', $admin->getUser()->getId())->update(['admin', false]);
                        }
                    }
                }
        
            });
        }
        
        if ($this->detectType() == 'message') {
            if ($this->getMessage()->has('new_chat_members')) {
                foreach ($this->getMessage()->getNewChatMembers() as $newChatMember) {
                    /** @var User $newChatMember */
                    // create new / update existing user
                    $user = MystUser::find($newChatMember->getId());
                    if ($user == null) {
                        $user = new MystUser();
                        $user->id = $newChatMember->getId();
                    }
                    $user = $user->updateUser($newChatMember);
    
                    // attach user to a chat (create a new chat member) if not already attached
                    $user->attachToChat($chat, $this->getChat());
                }
            }
            
            if ($this->getMessage()->has('left_chat_member')) {
                $tg_user = $this->getMessage()->getLeftChatMember();
                MystChatMember::where('user_id', $tg_user->getId())->where('chat_id', $this->getChat()->getId())->delete();
            }
        }
    }
    
    public function hasRestrictions()
    {
        /** @var MystUser $user
         * @var MystChat $chat
         * @var MystChatMember $member
         */
        
        $chat = MystChat::find($this->getChat()->getId());
        if ($chat == null) return true;
        if ($chat->Restriction !== null) {
            if ($chat->Restriction->respond) {
                $request = SendMessage::make()->to($this->getChat()->getId())->text("Chat Restricted.\nReason: " . $chat->Restriction->reason);
                if ($this->detectType() == 'message' || $this->detectType() == 'edited_message' || $this->detectType() == 'channel_post' || $this->detectType() == 'edited_channel_post')
                    $request->replyTo($this->getMessage()->getId());
                
                $this->getBot()->sendRequest($request);
            }
            return true;
        }
        
        $user = MystUser::find($this->getFrom()->getId());
        if ($user == null) return true;
        if ($user->Restriction !== null) {
            if ($user->Restriction->respond) {
                $request = SendMessage::make()->to($this->getChat()->getId())->text("User Restricted.\nReason: " . $user->Restriction->reason);
                if ($this->detectType() == 'message' || $this->detectType() == 'edited_message' || $this->detectType() == 'channel_post' || $this->detectType() == 'edited_channel_post')
                    $request->replyTo($this->getMessage()->getId());
            
                $this->getBot()->sendRequest($request);
            }
            return true;
        }
    
        $member = MystChatMember::where('chat_id', $this->getChat()->getId())->where('user_id', $this->getFrom()->getId())->first();
        if ($member == null) return true;
        if ($member->Restriction !== null) {
            if ($member->Restriction->respond) {
                $request = SendMessage::make()->to($this->getChat()->getId())->text("Chat Member Restricted.\nReason: " . $member->Restriction->reason);
                if ($this->detectType() == 'message' || $this->detectType() == 'edited_message' || $this->detectType() == 'channel_post' || $this->detectType() == 'edited_channel_post')
                    $request->replyTo($this->getMessage()->getId());
            
                $this->getBot()->sendRequest($request);
            }
            return true;
        }
        
        return false;
    }
    
    
    protected function processConversations()
    {
        if ($this->bot->getConfig('process.conversations') == false)  return true;
    
        if ($this->detectType() !== 'message') return true;
    
        if ($this->getMessage()->getReplyToMessage() === null) return true;
    
        $conversationService = new ConversationService();
        if (!$conversationService->hasConversation($this->getChat()->getId(), $this->getFrom()->getId())) return true;
        
        $convo = $conversationService->getConversation($this->getChat()->getId(), $this->getFrom()->getId());
        
        if ($this->getMessage()->getReplyToMessage()->getId() !== $convo['reply_message_id']) return true;
        
        if ($this->bot->getConversationsStack()->getStackItem($convo['name']) === null) return true;
        
        $conversation = $this->bot->getConversationsStack()->getStackItem($convo['name']);
        
        return $conversation->make($this->bot, $this, $convo);
    }
    
    protected function processCommands()
    {
        if ($this->bot->getConfig('process.commands') == false)  return true;
        
        if ($this->detectType() !== 'message' && $this->detectType() !== 'edited_message' && $this->detectType() !== 'channel_post' && $this->detectType() !== 'edited_channel_post') return true;
        
        if (!$this->getMessage()->has('entities')) return true;
        
        foreach ($this->bot->getCommandsStack()->getStack() as $name => $command) {
            /**@var CommandController $command*/
            foreach ($this->getMessage()->getEntities() as $entity) {
                /**@var Entity $entity*/
                if ($entity->getType() !== 'bot_command') continue;
    
                if (array_get($command->getEngagesIn(), $this->getChat()->getType()) == false) continue;
    
                if ($command->isStandalone() && strtolower($this->getMessage()->getText()) !== str_start(strtolower($name), '/')) continue;
                
                if (strtolower($entity->getText($this->getMessage()->getText())) !== str_start(strtolower($name), '/')) continue;
                
                if ($command->isCaseSensitive() && $entity->getText($this->getMessage()->getText()) !== str_start($name, '/')) continue;
                
                if (!$this->entityInPosition($this->getMessage()->getText(), $command->getPosition(), $entity->getOffset(), $entity->getLength())) continue;
                
                if ($command->isStandalone()) {
                    $args = [];
                } else {
                    $args = $this->getArgs(substr($this->getMessage()->getText(), $entity->getOffset() + $entity->getLength()), $this->bot->getConfig('commands_param_separator'));
                }
                
                return $command->make($this->bot, $this, $args);
            }
        }
        return true;
    }
    
    protected function processCallbackQueries()
    {
        if ($this->bot->getConfig('process.callback_queries') == false)  return true;
    
        if ($this->detectType() !== 'callback_query') return true;
    
        foreach ($this->bot->getCallbackQueriesStack()->getStack() as $name => $cbq) {
            /**@var CallbackQueryController $cbq*/
        
            if (array_get($cbq->getEngagesIn(), $this->getChat()->getType()) == false) continue;
            
            if ($this->getCallbackQuery()->getData() !== $name && !starts_with($this->getCallbackQuery()->getData(), $name . $this->bot->getConfig('cbq_param_separator'))) continue;
            
            $args = $this->getArgs(substr($this->getCallbackQuery()->getData(), strlen($name)), $this->bot->getConfig('cbq_param_separator'));
        
            return $cbq->make($this->bot, $this, $args);
        }
        return true;
    }
    
    protected function processHashtags()
    {
        if ($this->bot->getConfig('process.hashtags') == false)  return true;
        
        if ($this->detectType() !== 'message' && $this->detectType() !== 'edited_message' && $this->detectType() !== 'channel_post' && $this->detectType() !== 'edited_channel_post') return true;
        
        if (!$this->getMessage()->has('entities')) return true;
        
        foreach ($this->bot->getHashtagsStack()->getStack() as $name => $hashtag) {
            /**@var HashtagController $hashtag*/
            foreach ($this->getMessage()->getEntities() as $entity) {
                /**@var Entity $entity*/
                if ($entity->getType() !== 'hashtag') continue;
                
                if (array_get($hashtag->getEngagesIn(), $this->getChat()->getType()) == false) continue;
                
                if ($hashtag->isStandalone() && strtolower($this->getMessage()->getText()) !== str_start(strtolower($name), '#')) continue;
                
                if (strtolower($entity->getText($this->getMessage()->getText())) !== str_start(strtolower($name), '#')) continue;
                
                if ($hashtag->isCaseSensitive() && $entity->getText($this->getMessage()->getText()) !== str_start($name, '#')) continue;
                
                if (!$this->entityInPosition($this->getMessage()->getText(), $hashtag->getPosition(), $entity->getOffset(), $entity->getLength())) continue;
                
                if ($hashtag->isStandalone()) {
                    $args = [];
                } else {
                    $args = $this->getArgs(substr($this->getMessage()->getText(), $entity->getOffset() + $entity->getLength()), $this->bot->getConfig('commands_param_separator')); //intentional
                }
                
                return $hashtag->make($this->bot, $this, $args);
            }
        }
        return true;
    }
    
    protected function processMentions()
    {
        if ($this->bot->getConfig('process.mentions') == false)  return true;
        
        if ($this->detectType() !== 'message' && $this->detectType() !== 'edited_message' && $this->detectType() !== 'channel_post' && $this->detectType() !== 'edited_channel_post') return true;
        
        if (!$this->getMessage()->has('entities')) return true;
        
        foreach ($this->bot->getMentionsStack()->getStack() as $name => $mention) {
            /**@var MentionController $mention*/
            foreach ($this->getMessage()->getEntities() as $entity) {
                /**@var Entity $entity*/
                if ($entity->getType() !== 'mention') continue;
                
                if (array_get($mention->getEngagesIn(), $this->getChat()->getType()) == false) continue;
                
                if ($mention->isStandalone() && strtolower($this->getMessage()->getText()) !== str_start(strtolower($name), '@')) continue;
                
                if (strtolower($entity->getText($this->getMessage()->getText())) !== str_start(strtolower($name), '@')) continue;
                
                if ($mention->isCaseSensitive() && $entity->getText($this->getMessage()->getText()) !== str_start($name, '@')) continue;
                
                if (!$this->entityInPosition($this->getMessage()->getText(), $mention->getPosition(), $entity->getOffset(), $entity->getLength())) continue;
                
                if ($mention->isStandalone()) {
                    $args = [];
                } else {
                    $args = $this->getArgs(substr($this->getMessage()->getText(), $entity->getOffset() + $entity->getLength()), $this->bot->getConfig('commands_param_separator')); //intentional
                }
                
                return $mention->make($this->bot, $this, $args);
            }
        }
        return true;
    }
    
    protected function processTexts()
    {
        if ($this->bot->getConfig('process.texts') == false)  return true;
        
        if ($this->detectType() !== 'message' && $this->detectType() !== 'edited_message' && $this->detectType() !== 'channel_post' && $this->detectType() !== 'edited_channel_post') return true;
        
        foreach ($this->bot->getTextsStack()->getStack() as $name => $text) {
            /** @var TextController $text*/
            if (array_get($text->getEngagesIn(), $this->getChat()->getType()) == false) continue;
            if (strpos(strtolower($this->getMessage()->getText()), strtolower($name)) === false) continue;
            
            if ($text->isStandalone() && strtolower($this->getMessage()->getText()) !== strtolower($name)) continue;
            
            if ($text->isCaseSensitive() && strpos($this->getMessage()->getText(), $name) === false) continue;
            
            if (!$this->entityInPosition($this->getMessage()->getText(), $text->getPosition(), strpos($this->getMessage()->getText(), $name), strlen($name))) continue;
            
            if ($text->isStandalone()) {
                $args = [];
            } else {
                $args = $this->getArgs(substr($this->getMessage()->getText(), strpos($this->getMessage()->getText(), $name) + strlen($name)), $this->bot->getConfig('commands_param_separator')); //intentional
            }
            
            return $text->make($this->bot, $this, $args);
        }
        return true;
    }
}