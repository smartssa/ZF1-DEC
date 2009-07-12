<?php
/**
 * @author      Darryl E. Clarke <darryl.clarke@flatlinesystems.net>
 * @copyright   2009 Darryl E. Clarke
 * @version     $Id$
 */

abstract class DEC_Rest {
    protected $baseUrl;
    protected $cacheTag;
    protected $apiKey;
    protected $apiSecret;
    protected $defaultOptions = array();
    protected $mode;

    private $method;

    private $_cache   = null;
    private $_dbModel = null;
    private $_logger  = null;

    function __construct($options = array()) {
        // handle options
        if ($options['logger'] instanceof Zend_Log) {
            $this->_logger = $options['logger'];
            $this->log('DEC_Rest: Logging enabled');
        }

        if ($options['cache'] instanceof Zend_Cache_Core) {
            $this->_cache = $options['cache'];
            $this->log('DEC_Rest: Caching enabled');
        }
    }

    abstract function generateToken($secret, $args);
    abstract function setupApi($args);
    abstract function callComplete($result);

    protected function call($method, $args = array())
    {
        require_once 'Zend/Rest/Client.php';
        $args = $this->setupApi($args);

        $this->method = $method;
        // method
        $client = new Zend_Rest_Client($this->baseUrl);
        $this->log("DEC_Rest: Making request to " . $this->baseUrl);

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
        $this->log("DEC_Rest: Final Arguments " . print_r($finalArgs, true));

        $client->api_sig($this->generateToken($this->apiSecret, $finalArgs));

        $result = $this->callComplete($client->get());
        $this->log("DEC_Rest: Got results from request");
        return $result;
    }

    protected function log($message, $level = Zend_Log::INFO)
    {
        if ($this->_logger) {
            $this->_logger->log($message, $level);
        }
    }

    protected function getCache()
    {

        if ($this->_cache) {
            $result = $this->_cache->load($this->cacheTag);
            if ($result) {
                $this->log('DEC_Rest: loaded cache id ' . $this->cacheTag);
                    
            } else {
                $this->log('DEC_Rest: failed to load cache id (likely expired or non existant) ' . $this->cacheTag);
                $result = false;
            }
        }
        return $result;
    }

    protected function saveCache($object)
    {
        if ($this->_cache) {
            $this->_cache->save($object, $this->cacheTag);
            $this->log('DEC_Rest: saved cache id ' . $this->cacheTag);
            return true;
        }
        $this->log('DEC_Rest: failed to save cache id ' . $this->cacheTag, Zend_Log::WARN);
        return false;
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

    public function setCacheTag($args) {
        ksort($args);
        $string = $this->apiSecret;
        $string .= $this->mode;
        foreach ($args as $key=>$value):
            $string .= $key . $value;
        endforeach;
        
        $this->cacheTag = $this->mode . md5($string);
        return $this;
    }

    public function setMode($mode) {
        $this->mode = $mode;
        return $this;
    }


}