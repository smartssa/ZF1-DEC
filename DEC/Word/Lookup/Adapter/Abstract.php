<?php
abstract class DEC_Word_Lookup_Adapter_Abstract
{
    protected $baseUri;
    protected $apiKey;
    protected $json = null;
    protected $sourceName;
    protected $sourceUrl;

    public function __construct($config) {
        // stuff goes here.
        $this->setupApi($config);
    }

    public function lookup($word, $wordObj) {
        if (! is_string($word)) {
            throw new DEC_Word_Lookup_Exception('$word is not a String');
        }
        // look it up!
        if ($wordObj instanceof DEC_Word) {
            // lets do it
            $wordObj->setDefinition($this->getDefinition($word));
            $wordObj->setRelated($this->getRelated($word));
            $wordObj->setExample($this->getExample($word));
            $wordObj->setPhonetic($this->getPhonetic($word));
            $wordObj->setSource($this->sourceName, $this->sourceUrl);
        } else {
            throw new DEC_Word_Lookup_Exception('$wordObj is not an instance of DEC_Word');
        }
        return $wordObj;
    }

    public function request($url) {
        $client = new Zend_Http_Client($url, array('strict' => false));
        if ($this->apiKey !== null) {
            $client->setHeaders('api_key', $this->apiKey);
        }
        $response = $client->request();
        $this->json = Zend_Json::decode($this->filterResponse($response->getBody()), Zend_Json::TYPE_OBJECT);
        $this->checkErrors($this->json);
        return $this->json;
    }


    abstract function setupApi($config);
    abstract function getDefinition($word);
    abstract function getRelated($word);
    abstract function getExample($word);
    abstract function getPhonetic($word);
    abstract function filterResponse($response);
    abstract function checkErrors($body);
}