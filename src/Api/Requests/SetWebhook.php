<?php

namespace Blaze\Myst\Api\Requests;

use Blaze\Myst\Exceptions\RequestException;

class SetWebhook extends BaseRequest
{
    /**
     * @return string
     */
    protected function responseObject() : string
    {
        return 'bool';
    }
    
    /**
     * @param string $url
     * @return $this
     * @throws RequestException
     */
    public function url(string $url)
    {
        if (filter_var(filter_var($url, FILTER_SANITIZE_URL), FILTER_VALIDATE_URL) === false) {
            throw new RequestException("Invalid url provided");
        }
        $this->params['url'] = $url;
        return $this;
    }
    
    /**
     * @param $certificate
     * @return $this
     */
    public function certificate($certificate)
    {
        // Validate url
        if (filter_var(filter_var($certificate, FILTER_SANITIZE_URL), FILTER_VALIDATE_URL) !== false) {
            $certificate = fopen($certificate, 'r');
        }
        $this->params['certificate'] = $certificate;
        return $this;
    }
    
    /**
     * @param int $connections
     * @return $this
     */
    public function maxConnections(int $connections)
    {
        $this->params['max_connections'] = $connections;
        return $this;
    }
    
    /**
     * @param array $types
     * @return $this
     */
    public function allowedUpdates(array $types)
    {
        $this->params['allowed_updates'] = json_encode($types);
        return $this;
    }
}
