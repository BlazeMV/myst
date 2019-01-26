<?php

namespace Blaze\Myst\Api\Requests;

use Blaze\Myst\Api\Objects\User;

class SendChatAction extends BaseRequest
{
    protected function responseObject() : string
    {
        return User::class;
    }
    
    /**
     * @param $chat_id
     * @return $this
     */
    public function to($chat_id)
    {
        $this->params['chat_id'] = $chat_id;
        return $this;
    }
    
    /**
     * @return $this
     */
    public function typing()
    {
        $this->params['action'] = 'typing';
        return $this;
    }
    
    /**
     * @return $this
     */
    public function uploadPhoto()
    {
        $this->params['action'] = 'upload_photo';
        return $this;
    }
    
    /**
     * @return $this
     */
    public function recordVideo()
    {
        $this->params['action'] = 'record_video';
        return $this;
    }
    
    /**
     * @return $this
     */
    public function uploadVideo()
    {
        $this->params['action'] = 'upload_video';
        return $this;
    }
    
    /**
     * @return $this
     */
    public function recordAudio()
    {
        $this->params['action'] = 'record_audio';
        return $this;
    }
    
    /**
     * @return $this
     */
    public function uploadAudio()
    {
        $this->params['action'] = 'upload_audio';
        return $this;
    }
    
    /**
     * @return $this
     */
    public function uploadDocument()
    {
        $this->params['action'] = 'upload_document';
        return $this;
    }
    
    /**
     * @return $this
     */
    public function findLocation()
    {
        $this->params['action'] = 'find_location';
        return $this;
    }
    
    /**
     * @return $this
     */
    public function uploadVideoNote()
    {
        $this->params['action'] = 'upload_video_note';
        return $this;
    }
    
    /**
     * @return $this
     */
    public function recordVideoNote()
    {
        $this->params['action'] = 'record_video_note';
        return $this;
    }
}
