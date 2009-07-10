<?php

/**
 * DEC_Rest
 */


abstract class DEC_Rest {
    protected $baseUrl;
    protected $token;
    protected $defaultOptions = array();

    private $restClient;
    private $method;

    function __construct() {
    }

    abstract function generateToken($secret, $args);
    abstract function setupApi($args);
    abstract function callComplete($result);
    
    protected function call($method, $args = array())
    {
        $args = $this->setupApi($args);
        $this->method = $method;
        return $this->request($args);
    }

    protected function request($args)
    {
        require_once 'Zend/Rest/Client.php';
        // method
        $client = new Zend_Rest_Client($this->baseUrl);
        $method = $this->method;

        $client->method($method);
        $client->api_key($this->apiKey);
        $finalArgs = $this->mergeOptions($args);

        foreach ($finalArgs as $key => $value) {
            $client->{$key}($value);
        }

        // extra params that Zend_Rest adds;
        $finalArgs['arg1']    = $method;
        $finalArgs['method']  = $method;
        $finalArgs['api_key'] = $this->apiKey;
        $finalArgs['rest']    = '1';
        
        $client->api_sig($this->generateToken($this->apiSecret, $finalArgs));
        
        $result = $this->callComplete($client->get());
        return $result;
    }

    protected function mergeOptions($args) {
        return array_merge($this->defaultOptions, $args);
    }
    
    public function setBaseUrl($url) {
        $this->baseUrl = $url;
        return $this;
    }

    public function setApiKey($apiKey) {
        $this->apiKey = $apiKey;
        return $this;
    }

    public function setApiSecret($secret) {
        $this->apiSecret = $secret;
        return $this;
    }

    public function setToken($token) {
        $this->token = $token;
        return $this;
    }

    public function setMode($mode) {
        $this->mode = $mode;
        return $this;
    }
    

}