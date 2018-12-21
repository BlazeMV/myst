<?php

namespace Blaze\Myst\Traits;

use Blaze\Myst\Api\Objects\ChatMember;
use Blaze\Myst\Api\Objects\User;
use Blaze\Myst\Services\ConfigService;
use Carbon\Carbon;
use Blaze\Myst\Api\Response;
use Blaze\Myst\Api\Objects\Update;
use Illuminate\Support\Facades\Schema;
use Blaze\Myst\Exceptions\MystException;
use Blaze\Myst\Api\Requests\SendMessage;
use Blaze\Myst\Api\Requests\GetChatAdministrators;
use Blaze\Myst\Laravel\Models\User as MystUser;
use Blaze\Myst\Laravel\Models\Chat as MystChat;
use Blaze\Myst\Laravel\Models\ChatMember as MystChatMember;

trait UpdateHandler
{
    /**
     * @return Update
     * @throws MystException
     */
    public function getWebhookUpdate()
    {
        $body = json_decode(file_get_contents('php://input'), true);
        return (new Update($body))->setBot($this);
    }
    
    
    /**
     * @param callable|null $pre_function
     * @return Update
     * @throws MystException
     */
    public function handleUpdate(callable $pre_function = null)
    {
        $update = $this->getWebhookUpdate();
    
        if (ConfigService::shouldMaintainDatabase()) {
            $this->updateDatabase($update);
            
            if ($this->hasRestrictions($update)) {
                return $update;
            }
        }
        
        $process = true;
        
        if (is_callable($pre_function)) {
            $process = $pre_function($update);
        }
        if ($process !== false) {
            $this->processControllers($update);
        }
        
        return $update;
    }
    
    
    /**
     * @param Update $update
     * @return bool
     */
    public function processControllers(Update $update)
    {
        if (($update->detectType() == 'edited_message' || $update->detectType() == 'edited_channel_post')
            && $this->getConfig('process_edited_messages') == false) {
            return false;
        }
        if (!$this->getConfig('engages_in.' . $update->getChat()->getType())) {
            return false;
        }
        
        $this->getConversationsStack()->processStack($update);
        $this->getCommandsStack()->processStack($update);
        $this->getCallbackQueriesStack()->processStack($update);
        $this->getHashtagsStack()->processStack($update);
        $this->getMentionsStack()->processStack($update);
        $this->getTextsStack()->processStack($update);
        
        return true;
    }
    
    /**
     * @param Update $update
     * @return bool
     * @throws \Blaze\Myst\Exceptions\RequestException
     */
    private function updateDatabase(Update $update)
    {
        /**
         * @var MystUser $user
         * @var MystChat $chat
         * @var MystChatMember $chat_member
         */
        
        // check for table existence
        if (!Schema::hasTable('myst_users')) {
            return true;
        }
        if (!Schema::hasTable('myst_chats')) {
            return true;
        }
        if (!Schema::hasTable('myst_chat_members')) {
            return true;
        }
        
        // create new / update existing user
        $user = MystUser::find($update->getFrom()->getId());
        if ($user == null) {
            $user = new MystUser();
            $user->id = $update->getFrom()->getId();
        }
        $user = $user->updateUser($update->getFrom());
        
        // create new / update existing chat
        $chat = MystChat::find($update->getChat()->getId());
        if ($chat == null) {
            $chat = new MystChat();
            $chat->id = $update->getChat()->getId();
        }
        $chat = $chat->updateChat($update->getChat());
        
        // attach user to a chat (create a new chat member) if not already attached
        $user->attachToChat($chat, $update->getChat());
        
        // update chat admins every 24 hours
        if (Carbon::parse(
            MystChatMember::where('chat_id', $update->getChat()->getId())
                ->orderBy('created_at')
                ->first()
                ->updated_at
        )->lessThan(
            Carbon::parse('-24 hours')
        )
        ) {
            $update->getBot()->sendRequest(
                GetChatAdministrators::make()
                    ->chat($update->getChat()->getId())
                    ->async(),
                function (Response $response) use ($update) {
                    if ($response->isOk()) {
                        $admins = $response->getResponseObject();
                        foreach ($admins as $admin) {
                            /** @var ChatMember $admin */
                            if ($admin->getStatus() == "creator" || $admin->getStatus() == "administrator") {
                                MystChatMember::where('chat_id', $update->getChat()->getId())
                                    ->where('user_id', $admin->getUser()->getId())
                                    ->update(['admin', true]);
                            } else {
                                MystChatMember::where('chat_id', $update->getChat()->getId())
                                    ->where('user_id', $admin->getUser()->getId())
                                    ->update(['admin', false]);
                            }
                        }
                    }
                }
            );
        }
        
        if ($update->detectType() == 'message') {
            if ($update->getMessage()->has('new_chat_members')) {
                foreach ($update->getMessage()->getNewChatMembers() as $newChatMember) {
                    /** @var User $newChatMember */
                    // create new / update existing user
                    $user = MystUser::find($newChatMember->getId());
                    if ($user == null) {
                        $user = new MystUser();
                        $user->id = $newChatMember->getId();
                    }
                    $user = $user->updateUser($newChatMember);
                    
                    // attach user to a chat (create a new chat member) if not already attached
                    $user->attachToChat($chat, $update->getChat());
                }
            }
            
            if ($update->getMessage()->has('left_chat_member')) {
                $tg_user = $update->getMessage()->getLeftChatMember();
                MystChatMember::where('user_id', $tg_user->getId())
                    ->where('chat_id', $update->getChat()->getId())
                    ->delete();
            }
        }
        
        return true;
    }
    
    
    /**
     * @param Update $update
     * @return bool
     * @throws \Blaze\Myst\Exceptions\RequestException
     */
    private function hasRestrictions(Update $update)
    {
        /**
         * @var MystUser $user
         * @var MystChat $chat
         * @var MystChatMember $member
         */
        
        // check for table existence
        if (!Schema::hasTable('myst_restrictions')) {
            return false;
        }
    
        $type = $update->detectType();
        
        $chat = MystChat::find($update->getChat()->getId());
        if ($chat == null) {
            return true;
        }
        if ($chat->Restriction !== null) {
            if ($chat->Restriction->respond) {
                $request = SendMessage::make()
                    ->to($update->getChat()->getId())
                    ->text("Chat Restricted.\nReason: " . $chat->Restriction->reason);
                if ($type == 'message'
                    || $type == 'edited_message'
                    || $type == 'channel_post'
                    || $type == 'edited_channel_post'
                ) {
                    $request->replyTo($update->getMessage()->getId());
                }
    
                $update->getBot()->sendRequest($request);
            }
            return true;
        }
        
        $user = MystUser::find($update->getFrom()->getId());
        if ($user == null) {
            return true;
        }
        if ($user->Restriction !== null) {
            if ($user->Restriction->respond) {
                $request = SendMessage::make()
                    ->to($update->getChat()->getId())
                    ->text("User Restricted.\nReason: " . $user->Restriction->reason);
                if ($type == 'message'
                    || $type == 'edited_message'
                    || $type == 'channel_post'
                    || $type == 'edited_channel_post'
                ) {
                    $request->replyTo($update->getMessage()->getId());
                }
                
                $update->getBot()->sendRequest($request);
            }
            return true;
        }
        
        $member = MystChatMember::where('chat_id', $update->getChat()->getId())
            ->where('user_id', $update->getFrom()->getId())
            ->first();
        if ($member == null) {
            return true;
        }
        if ($member->Restriction !== null) {
            if ($member->Restriction->respond) {
                $request = SendMessage::make()
                    ->to($update->getChat()->getId())
                    ->text("Chat Member Restricted.\nReason: " . $member->Restriction->reason);
                if ($type == 'message'
                    || $type == 'edited_message'
                    || $type == 'channel_post'
                    || $type == 'edited_channel_post'
                ) {
                    $request->replyTo($update->getMessage()->getId());
                }
                
                $update->getBot()->sendRequest($request);
            }
            return true;
        }
        
        return false;
    }
}
